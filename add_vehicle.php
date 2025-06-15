<?php
// File: add_vehicle.php

// 1) Start/resume session, connect to DB, load helpers
require_once 'inc/session.php';
require 'inc/config.php';
require 'inc/functions.php';

// 2) Force login (redirect to login.php if not signed in)
ensureLoggedIn();

// 3) Discover the columns in the 'vehicles' table
$columns = [];
$colQuery = $conn->query("SHOW COLUMNS FROM vehicles");
if ($colQuery) {
    while ($col = $colQuery->fetch_assoc()) {
        $colName = $col['Field'];
        // Skip the primary key 'id' and the foreign key 'user_id'
        if ($colName === 'id' || $colName === 'user_id') {
            continue;
        }
        // We'll prompt for every other column
        $columns[] = $colName;
    }
    $colQuery->close();
}

// 4) Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare an array of values, in the same order as $columns
    $values = [];
    $placeholders = [];
    $types = ''; // For bind_param: 's' for every column (string)

    foreach ($columns as $colName) {
        // Grab submitted value (or empty string if not set)
        $val = trim($_POST[$colName] ?? '');
        $values[] = $val;
        $placeholders[] = '?';
        $types .= 's';  // treat each column as string
    }

    // Now build the INSERT statement dynamically
    // INSERT INTO vehicles (user_id, col1, col2, ...) VALUES (?, ?, ?, ...)
    $allCols = array_merge(['user_id'], $columns);
    $colList = implode(', ', $allCols);
    $phList  = implode(', ', array_merge(['?'], $placeholders));

    $sql = "INSERT INTO vehicles ($colList) VALUES ($phList)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters: first one is user_id (integer), then all strings
        // So we build a types string: 'i' . (string repeated for each extra column)
        $bindTypes = 'i' . $types;
        $bindValues = array_merge([$_SESSION['user_id']], $values);

        // mysqli_stmt::bind_param requires parameters by reference
        $refs = [];
        foreach ($bindValues as $key => $val) {
            $refs[$key] = &$bindValues[$key];
        }
        array_unshift($refs, $bindTypes);
        call_user_func_array([$stmt, 'bind_param'], $refs);

        if ($stmt->execute()) {
            header("Location: vehicles.php");
            exit;
        } else {
            $error = "Database error: " . htmlentities($stmt->error);
        }
        $stmt->close();
    } else {
        $error = "Failed to prepare statement: " . htmlentities($conn->error);
    }
}
?>

<?php include 'inc/header.php'; ?>

<h2>Add a New Vehicle</h2>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlentities($error) ?></div>
<?php endif; ?>

<form method="post" action="add_vehicle.php">
  <?php foreach ($columns as $colName): ?>
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
  <?php endforeach; ?>
  <button class="btn btn-success" type="submit">Add Vehicle</button>
  <a href="vehicles.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include 'inc/footer.php'; ?>
