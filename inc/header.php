<?php
// File: inc/header.php
// ————————————————————————————————————————————————————————
// Include after require 'inc/session.php'; require 'inc/config.php'; require 'inc/functions.php';
// Must be the very first HTML sent in each page.
// ————————————————————————————————————————————————————————
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Car Parking System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap 5 CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Optional: Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom CSS -->
  <style>
    body {
      background-color: #f4f6f9;
    }
    nav.navbar {
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .container {
      margin-top: 2rem;
    }
    footer {
      background-color: #0d6efd;
      color: #fff;
      padding: 1rem 0;
      text-align: center;
      margin-top: 4rem;
    }
    .table thead {
      background-color: #0d6efd;
      color: #fff;
    }
    .table-striped > tbody > tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.03);
    }
    .btn-outline-light {
      color: #0d6efd;
      border-color: #0d6efd;
    }
    .btn-outline-light:hover {
      background-color: #0d6efd;
      color: #fff;
    }
    .card {
      border: none;
      border-radius: 0.5rem;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="/car_park/">
      <i class="bi bi-car-front-fill me-2"></i> Car Park System
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <!-- Admin Menu -->
            <li class="nav-item"><a class="nav-link" href="/car_park/admin/dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/admin/manage_users.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/admin/manage_slots.php">Slots</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/admin/manage_slot_types.php">Slot Categories</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/admin/manage_bookings.php">Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/admin/manage_feedback.php">Feedback</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/admin/audit_logs.php">Audit Logs</a></li>

          <?php else: ?>
            <!-- Regular User Menu -->
            <li class="nav-item"><a class="nav-link" href="/car_park/user_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/vehicles.php">My Vehicles</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/book_slot.php">Book Slot</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/booking_history.php">My Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="/car_park/feedback.php">Feedback</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="/car_park/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/car_park/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/car_park/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
