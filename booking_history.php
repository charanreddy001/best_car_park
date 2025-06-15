<?php
// booking_history.php (fixed)
require_once 'inc/session.php';  // Start session here
require 'inc/config.php';
require 'inc/functions.php';
ensureLoggedIn();

$user_id = $_SESSION['user_id'];  // Now valid because session is active
// ... rest of code ...

// Fetch bookings for user
$stmt = $conn->prepare("
    SELECT b.id, b.created_at, b.payment_method, b.total_cost,
           v.plate_no, s.slot_name, t.type_name
    FROM bookings b
    JOIN vehicles v ON b.vehicle_id = v.id
    JOIN slots s    ON b.slot_id = s.id
    JOIN slot_types t ON s.type_id = t.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$results = $stmt->get_result();

include 'inc/header.php';
?>
<h2>My Booking History</h2>
<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>ID</th><th>Date</th><th>Vehicle</th>
      <th>Slot</th><th>Category</th><th>Payment</th><th>Cost</th><th>Feedback</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $results->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['created_at'] ?></td>
      <td><?= htmlentities($row['plate_no']) ?></td>
      <td><?= htmlentities($row['slot_name']) ?></td>
      <td><?= htmlentities($row['type_name']) ?></td>
      <td><?= $row['payment_method'] ?></td>
      <td>$<?= number_format($row['total_cost'],2) ?></td>
      <td>
        <a href="feedback.php?booking_id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">
          Give Feedback
        </a>
      </td>
    </tr>
    <?php endwhile; ?>
    <?php if ($results->num_rows === 0): ?>
      <tr><td colspan="8">No bookings found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
<?php include 'inc/footer.php'; ?>
