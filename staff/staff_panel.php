<?php
// staff/staff_panel.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header('Location: ../index.php'); exit;
}
require_once '../includes/db_connect.php';

// Handle status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $bid = intval($_POST['booking_id']);
    $action = $_POST['action'];
    if ($action == 'checkin') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'Checked-in' WHERE id = ?");
        $stmt->bind_param("i", $bid);
        $stmt->execute();
    } elseif ($action == 'checkout') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'Completed' WHERE id = ?");
        $stmt->bind_param("i", $bid);
        $stmt->execute();
    }
}

// Fetch bookings that are Confirmed or Checked-in
$sql = "SELECT b.id, u.name as user, s.name as slot, b.booking_date, b.status 
        FROM bookings b 
        JOIN users u ON b.user_id=u.id 
        JOIN slots s ON b.slot_id=s.id 
        WHERE b.status IN ('Confirmed','Checked-in')
        ORDER BY b.booking_date DESC";
$bookings = $conn->query($sql);
?>
<?php include '../includes/header.php'; ?>

<h2>Security Staff Panel</h2>
<table class="table">
  <thead><tr><th>ID</th><th>Customer</th><th>Slot</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
  <tbody>
    <?php while ($b = $bookings->fetch_assoc()): ?>
      <tr>
        <td><?php echo $b['id']; ?></td>
        <td><?php echo htmlspecialchars($b['user']); ?></td>
        <td><?php echo htmlspecialchars($b['slot']); ?></td>
        <td><?php echo $b['booking_date']; ?></td>
        <td><?php echo $b['status']; ?></td>
        <td>
          <?php if ($b['status'] == 'Confirmed'): ?>
            <form method="POST" style="display:inline">
              <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
              <button name="action" value="checkin" class="btn btn-primary btn-sm">Check-in</button>
            </form>
          <?php elseif ($b['status'] == 'Checked-in'): ?>
            <form method="POST" style="display:inline">
              <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
              <button name="action" value="checkout" class="btn btn-success btn-sm">Check-out</button>
            </form>
          <?php else: ?>
            <span class="text-muted">N/A</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include '../includes/footer.php'; ?>
