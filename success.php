<?php
// success.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header('Location: index.php'); exit;
}
require_once 'includes/db_connect.php';

$booking_id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT b.id, s.name, b.booking_date FROM bookings b JOIN slots s ON b.slot_id=s.id WHERE b.id = ? AND b.user_id = ?");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
if (!$booking) {
    die("Invalid booking or access denied.");
}
?>
<?php include 'includes/header.php'; ?>

<h2>Booking Confirmed!</h2>
<p>Your booking (ID <?php echo $booking['id']; ?>) has been confirmed.</p>
<p><strong>Slot:</strong> <?php echo htmlspecialchars($booking['name']); ?><br>
<strong>Date:</strong> <?php echo $booking['booking_date']; ?></p>
<p>Show this QR code when you arrive:</p>
<img src="generateQR.php?code=<?php echo urlencode("Booking ID: {$booking['id']}"); ?>" alt="QR Code">

<?php include 'includes/footer.php'; ?>
