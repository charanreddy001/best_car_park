<?php
// index.php — Landing Page for Car Parking System
// No sessions are needed here; this is just a public welcome page.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Car Parking System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Bootstrap 5 CSS (CDN) -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />

  <!-- Optional: Bootstrap Icons -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    rel="stylesheet"
  />

  <!-- Custom CSS (if you have any) -->
  <style>
    body {
      background-color: #f8f9fa;
    }
    .hero {
      background: url('assets/images/parking-hero.jpg') center/cover no-repeat;
      height: 60vh;
      position: relative;
      color: #fff;
    }
    .hero-overlay {
      position: absolute;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .hero-content {
      position: relative;
      z-index: 2;
    }
    .feature-icon {
      font-size: 3rem;
      color: #0d6efd;
    }
    footer {
      background-color: #0d6efd;
      color: #fff;
      padding: 1rem 0;
      text-align: center;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <i class="bi bi-car-front-fill me-2"></i>
        Car Park System
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Register</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <header class="hero d-flex align-items-center justify-content-center">
    <div class="hero-overlay"></div>
    <div class="container text-center hero-content">
      <h1 class="display-4 fw-bold">Welcome to Car Parking System</h1>
      <p class="lead">
        Seamlessly book, manage, and pay for your parking—anytime, anywhere.
      </p>
      <a href="login.php" class="btn btn-lg btn-primary me-2">Login</a>
      <a href="register.php" class="btn btn-lg btn-outline-light">Register</a>
    </div>
  </header>

  <!-- Features Section -->
  <section class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Features</h2>
      <div class="row text-center">
        <!-- Feature: Real-Time Availability -->
        <div class="col-md-4 mb-4">
          <i class="bi bi-speedometer2 feature-icon"></i>
          <h4 class="mt-3">Real-Time Availability</h4>
          <p>
            View live slot status—know which parking spaces are free or occupied
            instantly.
          </p>
        </div>

        <!-- Feature: Easy Booking -->
        <div class="col-md-4 mb-4">
          <i class="bi bi-calendar-check feature-icon"></i>
          <h4 class="mt-3">Easy Booking</h4>
          <p>
            Reserve your spot in advance through a simple booking flow—no queues,
            no hassle.
          </p>
        </div>

        <!-- Feature: Secure Payments -->
        <div class="col-md-4 mb-4">
          <i class="bi bi-wallet2 feature-icon"></i>
          <h4 class="mt-3">Secure Payments</h4>
          <p>
            Choose from cash, card, or UPI for seamless, secure payment
            transactions.
          </p>
        </div>
      </div>
      <div class="row text-center">
        <!-- Feature: Vehicle Management -->
        <div class="col-md-4 mb-4">
          <i class="bi bi-car-front feature-icon"></i>
          <h4 class="mt-3">Vehicle Management</h4>
          <p>
            Register and manage multiple vehicles under your account—
            detailed tracking at your fingertips.
          </p>
        </div>

        <!-- Feature: Slot Categories -->
        <div class="col-md-4 mb-4">
          <i class="bi bi-grid feature-icon"></i>
          <h4 class="mt-3">Slot Categories</h4>
          <p>
            Park in Standard, VIP, or Premium slots with different rates tailored
            to your needs.
          </p>
        </div>

        <!-- Feature: Feedback & Ratings -->
        <div class="col-md-4 mb-4">
          <i class="bi bi-star feature-icon"></i>
          <h4 class="mt-3">Feedback & Ratings</h4>
          <p>
            Share your experience—help us improve with ratings and comments after
            every visit.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="bg-light py-5">
    <div class="container">
      <h2 class="text-center mb-4">How It Works</h2>
      <div class="row text-center">
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
              <i class="bi bi-person-check-fill display-4 text-primary"></i>
              <h5 class="card-title mt-3">1. Register & Login</h5>
              <p class="card-text">
                Sign up with email and create your secure account—then log in to
                get started.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
              <i class="bi bi-car-front-fill display-4 text-primary"></i>
              <h5 class="card-title mt-3">2. Add Vehicles</h5>
              <p class="card-text">
                Add your vehicle details (license plate, model) under “My Vehicles”
                for easy booking.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
              <i class="bi bi-ticket-perforated display-4 text-primary"></i>
              <h5 class="card-title mt-3">3. Book & Pay</h5>
              <p class="card-text">
                Select a slot category, choose an available slot, and complete
                payment to confirm your booking.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer Section -->
  <footer>
    <div class="container">
      <p class="mb-1">&copy; <?= date('Y') ?> Car Parking System</p>
      <small>Designed for a seamless parking experience.</small>
    </div>
  </footer>

  <!-- Bootstrap 5 JS Bundle (Popper included) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
