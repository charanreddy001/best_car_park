<?php
// File: admin/manage_slots.php
require_once '../inc/session.php';
require_once '../inc/config.php';
require_once '../inc/functions.php';

// Only allow admins here
ensureAdmin();

// 1) Discover the columns in the 'slots' table
$columns = [];
$colQuery = $conn->query("SHOW COLUMNS FROM slots");
if ($colQuery) {
    while ($col = $colQuery->fetch_assoc()) {
        $colName = $col['Field'];
        // Skip the primary key 'id'
        if ($colName === 'id') {
            continue;
        }
        $columns[] = $colName;
    }
    $colQuery->close();
}

// 2) Handle form submission to add a new slot
if (isset($_POST['add_slot'])) {
    // Build a placeholder list and values array
    $placeholders = [];
    $types = '';      // types string for bind_param
    $values = [];

    // 'id' is auto-increment, so skip it; use columns[] for rest
    foreach ($columns as $colName) {
        $val = trim($_POST[$colName] ?? '');
        $placeholders[] = '?';
        $types .= 's';       // treat all as string for simplicity
        $values[] = $val;
    }

    // Build SQL: INSERT INTO slots (col1, col2, ...) VALUES (?, ?, ...)
    $colList = implode(', ', $columns);
    $phList  = implode(', ', $placeholders);
    $sql = "INSERT INTO slots ($colList) VALUES ($phList)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // bind_param requires references
        $refs = [];
        foreach ($values as $k => $v) {
            $refs[$k] = &$values[$k];
        }
        array_unshift($refs, $types);
        call_user_func_array([$stmt, 'bind_param'], $refs);

        if ($stmt->execute()) {
            header("Location: manage_slots.php");
            exit;
        } else {
            $error = "Database error: " . htmlentities($stmt->error);
        }
        $stmt->close();
    } else {
        $error = "Prepare failed: " . htmlentities($conn->error);
    }
}

// 3) Handle slot edits
if (isset($_POST['edit_slot'])) {
    $slot_id = intval($_POST['slot_id']);
    $setClauses = [];
    $types = '';
    $values = [];

    // Generate "col = ?" and collect values, skipping 'id'
    foreach ($columns as $colName) {
        $val = trim($_POST[$colName] ?? '');
        $setClauses[] = "$colName = ?";
        $types .= 's';
        $values[] = $val;
    }
    // Finally append the slot_id for WHERE
    $types .= 'i';
    $values[] = $slot_id;

    $setList = implode(', ', $setClauses);
    $sql = "UPDATE slots SET $setList WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // bind_param requires references
        $refs = [];
        foreach ($values as $k => $v) {
            $refs[$k] = &$values[$k];
        }
        array_unshift($refs, $types);
        call_user_func_array([$stmt, 'bind_param'], $refs);

        if ($stmt->execute()) {
            header("Location: manage_slots.php");
            exit;
        } else {
            $error = "Database error: " . htmlentities($stmt->error);
        }
        $stmt->close();
    } else {
        $error = "Prepare failed: " . htmlentities($conn->error);
    }
}

// 4) Handle slot deletion
if (isset($_POST['delete_slot'])) {
    $slot_id = intval($_POST['slot_id']);
    $stmt = $conn->prepare("DELETE FROM slots WHERE id = ?");
    $stmt->bind_param("i", $slot_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_slots.php");
    exit;
}

// 5) Fetch all slots
$slots = [];
$res = $conn->query("SELECT * FROM slots ORDER BY id ASC");
while ($row = $res->fetch_assoc()) {
    $slots[] = $row;
}
$res->close();
?>

<?php include '../inc/header.php'; ?>

<h2 class="mb-4">Manage Slots</h2>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlentities($error) ?></div>
<?php endif; ?>

<!-- Button to toggle “Add Slot” form -->
<button class="btn btn-success mb-3" data-bs-toggle="collapse" data-bs-target="#addSlotForm">
  <i class="bi bi-plus-circle"></i> Add New Slot
</button>

<div id="addSlotForm" class="collapse mb-4">
  <div class="card card-body">
    <form method="post">
      <div class="row g-3">
        <?php foreach ($columns as $colName): ?>
          <div class="col-md-6 mb-3">
            <label class="form-label"><?= htmlentities(ucwords(str_replace('_', ' ', $colName))) ?></label>
            <input
              type="text"
              name="<?= htmlentities($colName) ?>"
              class="form-control"
              value="<?= htmlentities($_POST[$colName] ?? '') ?>"
              required
            >
          </div>
        <?php endforeach; ?>
        <div class="col-12">
          <button type="submit" name="add_slot" class="btn btn-primary">Add Slot</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Slots Table -->
<table class="table table-striped table-bordered align-middle">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <?php foreach ($columns as $colName): ?>
        <th><?= htmlentities(ucwords(str_replace('_', ' ', $colName))) ?></th>
      <?php endforeach; ?>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($slots as $s): ?>
      <tr>
        <td><?= $s['id'] ?></td>
        <?php foreach ($columns as $colName): ?>
          <td><?= htmlentities($s[$colName]) ?></td>
        <?php endforeach; ?>
        <td>
          <!-- Edit Button triggers modal -->
          <button
            class="btn btn-sm btn-warning me-2"
            data-bs-toggle="modal"
            data-bs-target="#editModal<?= $s['id'] ?>"
          >
            <i class="bi bi-pencil-square"></i> Edit
          </button>

          <!-- Delete Form -->
          <form method="post" class="d-inline" onsubmit="return confirm('Delete slot #<?= $s['id'] ?>?');">
            <input type="hidden" name="slot_id" value="<?= $s['id'] ?>">
            <button type="submit" name="delete_slot" class="btn btn-sm btn-danger">
              <i class="bi bi-trash"></i> Delete
            </button>
          </form>

          <!-- Edit Modal -->
          <div
            class="modal fade"
            id="editModal<?= $s['id'] ?>"
            tabindex="-1"
            aria-labelledby="editModalLabel<?= $s['id'] ?>"
            aria-hidden="true"
          >
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title" id="editModalLabel<?= $s['id'] ?>">
                    Edit Slot #<?= $s['id'] ?>
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <form method="post" id="editForm<?= $s['id'] ?>">
                    <input type="hidden" name="slot_id" value="<?= $s['id'] ?>">
                    <?php foreach ($columns as $colName): ?>
                      <div class="mb-3">
                        <label class="form-label"><?= htmlentities(ucwords(str_replace('_', ' ', $colName))) ?></label>
                        <input
                          type="text"
                          name="<?= htmlentities($colName) ?>"
                          class="form-control"
                          value="<?= htmlentities($s[$colName]) ?>"
                          required
                        >
                      </div>
                    <?php endforeach; ?>
                  </form>
                </div>
                <div class="modal-footer">
                  <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                  >Cancel</button>
                  <button
                    type="submit"
                    form="editForm<?= $s['id'] ?>"
                    name="edit_slot"
                    class="btn btn-primary"
                  >Save Changes</button>
                </div>
              </div>
            </div>
          </div>
          <!-- End Edit Modal -->

        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include '../inc/footer.php'; ?>
