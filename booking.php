<?php
// booking.php (Customer)
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header('Location: index.php'); exit;
}
require_once 'includes/db_connect.php';

$selected_date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
$booked_slots = [];

// Fetch booked slot IDs for the selected date
$stmt = $conn->prepare("SELECT slot_id FROM bookings WHERE booking_date = ?");
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $booked_slots[] = $row['slot_id'];
}

// Fetch all slots
$slots = [];
$result = $conn->query("SELECT id, name FROM slots");
while ($row = $result->fetch_assoc()) {
    $slots[$row['id']] = $row['name'];
}

// Handle booking form submission
if (isset($_POST['slot_id'])) {
    $slot_id = $_POST['slot_id'];
    $user_id = $_SESSION['user_id'];

    // Insert new booking (status Pending until payment):contentReference[oaicite:17]{index=17}
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, slot_id, booking_date, status, paid) VALUES (?, ?, ?, 'Pending', 0)");
    $stmt->bind_param("iis", $user_id, $slot_id, $selected_date);
    $stmt->execute();
    $booking_id = $conn->insert_id;
    // Redirect to payment simulation
    header("Location: payment.php?id=$booking_id");
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<h2>Book a Parking Slot</h2>
<p>Select a date to see available slots:</p>
<form method="POST" action="booking.php" class="mb-3 row">
  <div class="col-md-4">
    <input type="date" name="date" class="form-control" value="<?php echo $selected_date; ?>" required>
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary">Check Availability</button>
  </div>
</form>

<h4>Slot Availability on <?php echo $selected_date; ?>:</h4>
<div class="mb-3">
  <?php foreach ($slots as $id => $name): ?>
    <?php if (in_array($id, $booked_slots)): ?>
      <span class="badge bg-danger m-1"><?php echo htmlspecialchars($name); ?> (Booked)</span>
    <?php else: ?>
      <span class="badge bg-success m-1"><?php echo htmlspecialchars($name); ?> (Free)</span>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<?php if (count($booked_slots) == count($slots)): ?>
  <div class="alert alert-warning">No slots available on this date.</div>
<?php else: ?>
  <form method="POST" action="booking.php">
    <input type="hidden" name="date" value="<?php echo $selected_date; ?>">
    <div class="mb-3">
      <label>Select a free slot:</label>
      <select name="slot_id" class="form-select" required>
        <option value="">-- Choose Slot --</option>
        <?php foreach ($slots as $id => $name): 
                if (!in_array($id, $booked_slots)): ?>
          <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
        <?php endif; endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-success">Book & Pay</button>
  </form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
