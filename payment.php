<?php
// payment.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header('Location: index.php'); exit;
}
require_once 'includes/db_connect.php';

$booking_id = intval($_GET['id'] ?? 0);
$message = '';

// Verify booking belongs to this user
$stmt = $conn->prepare("SELECT b.id, s.name, b.booking_date FROM bookings b JOIN slots s ON b.slot_id=s.id WHERE b.id = ? AND b.user_id = ?");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    die("Invalid booking ID.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate payment success: update booking
    $stmt = $conn->prepare("UPDATE bookings SET status='Confirmed', paid=1 WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    // Redirect to success page with QR
    header("Location: success.php?id=$booking_id");
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<h2>Payment</h2>
<p>Please confirm your booking:</p>
<ul class="list-group">
  <li class="list-group-item"><strong>Booking ID:</strong> <?php echo $booking['id']; ?></li>
  <li class="list-group-item"><strong>Slot:</strong> <?php echo htmlspecialchars($booking['name']); ?></li>
  <li class="list-group-item"><strong>Date:</strong> <?php echo $booking['booking_date']; ?></li>
  <li class="list-group-item"><strong>Amount:</strong> $10.00 (simulated)</li>
</ul>
<form method="POST" action="payment.php?id=<?php echo $booking_id; ?>" class="mt-3">
  <button type="submit" class="btn btn-primary">Confirm Payment</button>
</form>

<?php include 'includes/footer.php'; ?>
