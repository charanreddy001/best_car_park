<?php
// user_dashboard.php

require_once 'inc/session.php';
require 'inc/config.php';
require 'inc/functions.php';

// Only allow logged‐in users
ensureLoggedIn();

// Restrict access to regular “user” role only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // If someone logged in as admin or has no role, send them to the admin dashboard (or wherever you prefer)
    header("Location: admin/dashboard.php");
    exit();
}

// Fetch user stats
$user_id = $_SESSION['user_id'];

// Count vehicles
$stmt = $conn->prepare("SELECT COUNT(*) FROM vehicles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($count_vehicles);
$stmt->fetch();
$stmt->close();

// Count bookings
$stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($count_bookings);
$stmt->fetch();
$stmt->close();
?>

<?php include 'inc/header.php'; ?>

<h2>Welcome, <?= htmlentities($_SESSION['username']); ?>!</h2>
<p class="lead">Use the menu to manage your vehicles, book parking slots, and view your history.</p>

<div class="row">
  <div class="col-md-4">
    <div class="card text-white bg-info mb-3">
      <div class="card-header">My Vehicles</div>
      <div class="card-body">
        <h5 class="card-title"><?= $count_vehicles ?></h5>
        <p class="card-text">You have <?= $count_vehicles ?> vehicle(s) registered.</p>
        <a href="vehicles.php" class="btn btn-light">Manage Vehicles</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-white bg-success mb-3">
      <div class="card-header">My Bookings</div>
      <div class="card-body">
        <h5 class="card-title"><?= $count_bookings ?></h5>
        <p class="card-text">Total bookings you made.</p>
        <a href="booking_history.php" class="btn btn-light">View Bookings</a>
      </div>
    </div>
  </div>
</div>

<?php include 'inc/footer.php'; ?>
