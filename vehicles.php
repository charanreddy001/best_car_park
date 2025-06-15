<?php
// File: vehicles.php

// 1) Start or resume the session, connect to DB, load helper functions
require_once 'inc/session.php';
require 'inc/config.php';
require 'inc/functions.php';

// 2) Force login (redirect to login.php if not signed in)
ensureLoggedIn();

// 3) (Optional) If you want ONLY “user” role to see this, uncomment below:
/*
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // If someone tries to visit as admin (or no role), send them to admin dashboard
    header("Location: admin/dashboard.php");
    exit();
}
*/

// 4) Now we know a user is logged in. Fetch their vehicles. We select ALL columns
//    so we do not make assumptions about column names like 'make' or 'model'.
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<?php include 'inc/header.php'; ?>

<h2>My Vehicles</h2>
<p>
  You are logged in as <strong><?= htmlentities($_SESSION['username']); ?></strong>
  (Role: <?= htmlentities($_SESSION['role']); ?>)
</p>

<?php if ($result->num_rows === 0): ?>
  <p>You have no vehicles registered. <a href="add_vehicle.php">Add a vehicle</a>.</p>
<?php else: ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <?php
        // Dynamically print column headers based on the first row's keys:
        $firstRow = $result->fetch_assoc();
        foreach (array_keys($firstRow) as $colName) {
            echo '<th>' . htmlentities($colName) . '</th>';
        }
        // Reset pointer so we can loop through all rows below
        $result->data_seek(0);
        ?>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <?php foreach ($row as $cell): ?>
            <td><?= htmlentities($cell) ?></td>
          <?php endforeach; ?>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php include 'inc/footer.php'; ?>
