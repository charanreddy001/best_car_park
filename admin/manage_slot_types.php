<?php
// admin/manage_slot_types.php

session_start();                  // ← start the session before anything else
require_once '../inc/config.php';
require_once '../inc/functions.php';

ensureAdmin(); // Only admins should access

// Add new slot type
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type_name'], $_POST['rate'])) {
    $name = $conn->real_escape_string($_POST['type_name']);
    $rate = floatval($_POST['rate']);

    $stmt = $conn->prepare("INSERT INTO slot_types (type_name, rate) VALUES (?, ?)");
    $stmt->bind_param("sd", $name, $rate);
    if ($stmt->execute()) {
        logAction($_SESSION['user_id'], "Added slot category '$name'");
    }
    $stmt->close();
    header("Location: manage_slot_types.php"); // Redirect after submit
    exit;
}

// Delete a slot type
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM slot_types WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    if ($stmt->execute()) {
        logAction($_SESSION['user_id'], "Deleted slot category ID $del_id");
    }
    $stmt->close();
    header("Location: manage_slot_types.php"); // Redirect after delete
    exit;
}

// Fetch all slot types
$result = $conn->query("SELECT * FROM slot_types");

include '../inc/header.php';
?>

<h2>Manage Slot Categories</h2>

<!-- Add Form -->
<form method="post" class="row gy-2 gx-2 mb-4">
  <div class="col-md-5">
    <input type="text" name="type_name" class="form-control" placeholder="Category Name (e.g. VIP)" required>
  </div>
  <div class="col-md-5">
    <input type="number" step="0.01" name="rate" class="form-control" placeholder="Rate" required>
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary w-100">Add Category</button>
  </div>
</form>

<!-- List Table -->
<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th><th>Name</th><th>Rate</th><th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlentities($row['type_name']) ?></td>
        <td>$<?= number_format($row['rate'], 2) ?></td>
        <td>
          <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Delete this category?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    <?php if ($result->num_rows == 0): ?>
      <tr><td colspan="4">No categories defined.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include '../inc/footer.php'; ?>
