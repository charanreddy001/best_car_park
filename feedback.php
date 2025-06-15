<?php
// File: feedback.php

require_once 'inc/session.php';
require 'inc/config.php';
require 'inc/functions.php';

// Force login
ensureLoggedIn();

$user_id = $_SESSION['user_id'];

// Fetch user bookings (only id, no date)
$stmt = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings_result = $stmt->get_result();
$stmt->close();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = trim($_POST['comment'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $booking_id = intval($_POST['booking_id'] ?? 0);

    // Validate inputs
    if ($booking_id <= 0) {
        $error = "Please select a valid booking.";
    } elseif ($rating < 1 || $rating > 5) {
        $error = "Please provide a valid rating between 1 and 5.";
    } elseif ($comment === '') {
        $error = "Comment cannot be empty.";
    } else {
        // Verify booking belongs to user
        $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count === 0) {
            $error = "Selected booking is invalid.";
        } else {
            // Insert feedback
            $stmt = $conn->prepare("INSERT INTO feedback (user_id, booking_id, comment, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("iisi", $user_id, $booking_id, $comment, $rating);
            $stmt->execute();
            $stmt->close();

            $success = "Thank you for your feedback!";
            // Clear POST data
            $_POST = [];
        }
    }
}

// Fetch past feedback from this user
$stmt = $conn->prepare("SELECT comment, rating, created_at, booking_id FROM feedback WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$feedback_list = $stmt->get_result();
$stmt->close();

?>

<?php include 'inc/header.php'; ?>

<h2>Feedback</h2>

<?php if ($success): ?>
  <div class="alert alert-success"><?= htmlentities($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlentities($error) ?></div>
<?php endif; ?>

<form method="post" action="feedback.php">

  <div class="mb-3">
    <label for="booking_id" class="form-label">Select Booking</label>
    <select name="booking_id" id="booking_id" class="form-select" required>
      <option value="">-- Select booking --</option>
      <?php foreach ($bookings_result as $booking): 
          $selected = ($_POST['booking_id'] ?? '') == $booking['id'] ? 'selected' : '';
      ?>
        <option value="<?= $booking['id'] ?>" <?= $selected ?>>
          Booking #<?= $booking['id'] ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label for="rating" class="form-label">Rating (1 to 5)</label>
    <select name="rating" id="rating" class="form-select" required>
      <option value="">-- Select rating --</option>
      <?php
      $selected_rating = $_POST['rating'] ?? '';
      for ($i = 1; $i <= 5; $i++) {
          $sel = ($selected_rating == $i) ? 'selected' : '';
          echo "<option value=\"$i\" $sel>$i</option>";
      }
      ?>
    </select>
  </div>

  <div class="mb-3">
    <label for="comment" class="form-label">Your Feedback</label>
    <textarea class="form-control" name="comment" id="comment" rows="3" required><?= htmlentities($_POST['comment'] ?? '') ?></textarea>
  </div>

  <button type="submit" class="btn btn-primary">Submit Feedback</button>
</form>

<hr>

<h3>Your Past Feedback</h3>

<?php if ($feedback_list->num_rows === 0): ?>
  <p>You haven’t left any feedback yet.</p>
<?php else: ?>
  <ul class="list-group">
    <?php while ($row = $feedback_list->fetch_assoc()): ?>
      <li class="list-group-item">
        <strong>
          <?= htmlentities($row['created_at']) ?> - Booking #<?= htmlentities($row['booking_id']) ?> - Rating: <?= htmlentities($row['rating']) ?>/5
        </strong><br>
        <?= nl2br(htmlentities($row['comment'])) ?>
      </li>
    <?php endwhile; ?>
  </ul>
<?php endif; ?>

<?php include 'inc/footer.php'; ?>
