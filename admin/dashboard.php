<?php
// admin/dashboard.php

// 1) Start the session and load shared config/functions
require_once '../inc/session.php';
require_once '../inc/config.php';
require_once '../inc/functions.php';

// 2) Restrict access to admins only
ensureAdmin();

// 3) Fetch aggregate statistics from the database

// 3.1 Total users
$total_users = 0;
$sql = "SELECT COUNT(*) AS cnt FROM users";
if ($result = $conn->query($sql)) {
    $row = $result->fetch_assoc();
    $total_users = (int) $row['cnt'];
    $result->free();
}

// 3.2 Total vehicles
$total_vehicles = 0;
$sql = "SELECT COUNT(*) AS cnt FROM vehicles";
if ($result = $conn->query($sql)) {
    $row = $result->fetch_assoc();
    $total_vehicles = (int) $row['cnt'];
    $result->free();
}

// 3.3 Total bookings
$total_bookings = 0;
$sql = "SELECT COUNT(*) AS cnt FROM bookings";
if ($result = $conn->query($sql)) {
    $row = $result->fetch_assoc();
    $total_bookings = (int) $row['cnt'];
    $result->free();
}

// 3.4 Average rating (assuming 'feedback' table has a numeric 'rating' column)
$avg_rating = '0.00';
$sql = "SELECT AVG(rating) AS avg_rating FROM feedback";
if ($result = $conn->query($sql)) {
    $row = $result->fetch_assoc();
    // Format to two decimal places
    $avg_rating = number_format((float) $row['avg_rating'], 2);
    $result->free();
}

?>
<?php include '../inc/header.php'; ?>

<div class="container mt-4">
  <h2>Admin Dashboard</h2>
  <p>Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
  <p>This is the admin panel. (Only users with role 'admin' can see this.)</p>

  <div class="row mt-4">
    <div class="col-md-3 mb-3">
      <div class="card text-white bg-secondary">
        <div class="card-body">
          <h5 class="card-title">Total Users</h5>
          <p class="card-text display-4"><?= $total_users ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title">Total Vehicles</h5>
          <p class="card-text display-4"><?= $total_vehicles ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h5 class="card-title">Total Bookings</h5>
          <p class="card-text display-4"><?= $total_bookings ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3 mb-3">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <h5 class="card-title">Average Rating</h5>
          <p class="card-text display-4"><?= $avg_rating ?></p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../inc/footer.php'; ?>
