<?php
// includes/header.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Parking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Parking System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="/car_parking_system/admin/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/car_parking_system/admin/user_management.php">User Management</a></li>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'staff'): ?>
          <li class="nav-item"><a class="nav-link" href="/car_parking_system/staff/staff_panel.php">Staff Panel</a></li>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'customer'): ?>
          <li class="nav-item"><a class="nav-link" href="/car_parking_system/booking.php">Book Slot</a></li>
          <li class="nav-item"><a class="nav-link" href="/car_parking_system/history.php">My Bookings</a></li>
          <li class="nav-item"><a class="nav-link" href="/car_parking_system/profile.php">Profile</a></li>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="/car_parking_system/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/car_parking_system/register.php">Register</a></li>
          <li class="nav-item"><a class="nav-link" href="/car_parking_system/index.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
