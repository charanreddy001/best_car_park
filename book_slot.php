<?php
// File: book_slot.php

// 1) Start/resume session, connect to DB, load helpers
require_once 'inc/session.php';
require 'inc/config.php';
require 'inc/functions.php';

// 2) Force login (redirects to login.php if not signed in)
ensureLoggedIn();

// 3) (Optional) If you want ONLY “user” role to book, uncomment:
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
//     header("Location: admin/dashboard.php");
//     exit();
// }

// 4) Discover all columns in the 'bookings' table
$bookingColumns = [];
$colQuery = $conn->query("SHOW COLUMNS FROM bookings");
if ($colQuery) {
    while ($col = $colQuery->fetch_assoc()) {
        $colName = $col['Field'];
        // Skip 'id' (auto‐increment) and 'user_id' (we set via session)
        if ($colName === 'id' || $colName === 'user_id') {
            continue;
        }
        $bookingColumns[] = $colName;
    }
    $colQuery->close();
}

// 5) Fetch vehicles for dropdown (if 'vehicle_id' is a booking column)
$vehicles = [];
if (in_array('vehicle_id', $bookingColumns)) {
    $stmtV = $conn->prepare("SELECT * FROM vehicles WHERE user_id = ?");
    $stmtV->bind_param("i", $_SESSION['user_id']);
    $stmtV->execute();
    $vehRes = $stmtV->get_result();
    while ($r = $vehRes->fetch_assoc()) {
        $vehicles[] = $r;
    }
    $stmtV->close();
}

// 6) Fetch slots for dropdown (if 'slot_id' is a booking column)
$slots = [];
if (in_array('slot_id', $bookingColumns)) {
    $slotRes = $conn->query("SELECT * FROM slots");
    if ($slotRes) {
        while ($s = $slotRes->fetch_assoc()) {
            $slots[] = $s;
        }
        $slotRes->close();
    }
}

// 7) Handle form submission: dynamically build INSERT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare arrays to build INSERT
    $insertCols = ['user_id'];      // first column always user_id
    $placeholders = ['?'];          // first placeholder
    $types = 'i';                   // 'i' for the user_id integer
    $values = [ $_SESSION['user_id'] ];

    // For each remaining booking column, grab POST value (or empty)
    foreach ($bookingColumns as $colName) {
        $insertCols[] = $colName;
        $placeholders[] = '?';
        $types    .= 's';                // treat every column as string
        $values[]  = trim($_POST[$colName] ?? '');
    }

    // Build the SQL: INSERT INTO bookings (col1, col2, ...) VALUES (?, ?, ...)
    $colList = implode(', ', $insertCols);
    $phList  = implode(', ', $placeholders);
    $sql = "INSERT INTO bookings ($colList) VALUES ($phList)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // bind_param needs references
        $refs = [];
        foreach ($values as $k => $v) {
            $refs[$k] = &$values[$k];
        }
        array_unshift($refs, $types);
        call_user_func_array([$stmt, 'bind_param'], $refs);

        if ($stmt->execute()) {
            $success = "Slot booked successfully!";
        } else {
            $error = "Database error: " . htmlentities($stmt->error);
        }
        $stmt->close();
    } else {
        $error = "Preparation failed: " . htmlentities($conn->error);
    }
}
?>

<?php include 'inc/header.php'; ?>

<h2>Book a Parking Slot</h2>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlentities($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
  <div class="alert alert-success"><?= htmlentities($success) ?></div>
<?php endif; ?>

<form method="post" action="book_slot.php">
  <?php
  // For each discovered booking column, render an appropriate input
  foreach ($bookingColumns as $colName):

      // If it's 'vehicle_id', show a dropdown of the user's vehicles
      if ($colName === 'vehicle_id'):
  ?>
      <div class="mb-3">
        <label class="form-label">Select Vehicle</label>
        <select class="form-select" name="vehicle_id" required>
          <option value="">-- Choose your vehicle --</option>
          <?php foreach ($vehicles as $v): ?>
            <option value="<?= htmlentities($v['id']) ?>"
              <?= (isset($_POST['vehicle_id']) && $_POST['vehicle_id'] == $v['id']) ? 'selected' : '' ?>>
              <?php
                // Build a label from all vehicle fields except 'id' & 'user_id'
                $parts = [];
                foreach ($v as $col => $val) {
                    if ($col === 'id' || $col === 'user_id') continue;
                    $parts[] = htmlentities($val);
                }
                echo implode(' | ', $parts);
              ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

  <?php
      // If it's 'slot_id', show a dropdown of all slots
      elseif ($colName === 'slot_id'):
  ?>
      <div class="mb-3">
        <label class="form-label">Select Slot</label>
        <select class="form-select" name="slot_id" required>
          <option value="">-- Choose a slot --</option>
          <?php foreach ($slots as $s): ?>
            <option value="<?= htmlentities($s['id']) ?>"
              <?= (isset($_POST['slot_id']) && $_POST['slot_id'] == $s['id']) ? 'selected' : '' ?>>
              <?php
                // Build a label from all slot fields except 'id'
                $parts = [];
                foreach ($s as $col => $val) {
                    if ($col === 'id') continue;
                    $parts[] = htmlentities($val);
                }
                echo implode(' | ', $parts);
              ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

  <?php
      // Otherwise, render a generic text input for that column
      else:
  ?>
      <div class="mb-3">
        <label class="form-label"><?= htmlentities(ucwords(str_replace('_', ' ', $colName))) ?></label>
        <input
          class="form-control"
          type="text"
          name="<?= htmlentities($colName) ?>"
          value="<?= htmlentities($_POST[$colName] ?? '') ?>"
          required
        >
      </div>
  <?php
      endif;
  endforeach;
  ?>

  <button class="btn btn-primary" type="submit">Book Slot</button>
</form>

<?php include 'inc/footer.php'; ?>
