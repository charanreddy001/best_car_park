<?php
// history.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header('Location: index.php'); exit;
}
require_once 'includes/db_connect.php';

// Handle filter inputs
$date_from = $_GET['from'] ?? '';
$date_to   = $_GET['to'] ?? '';
$status    = $_GET['status'] ?? '';

// Build query with filters
$query = "SELECT b.id, s.name, b.booking_date, b.status 
          FROM bookings b JOIN slots s ON b.slot_id=s.id 
          WHERE b.user_id = ?";
$params = [$ _SESSION['user_id']];
$types = "i";

if ($status != "") {
    $query .= " AND b.status = ?";
    $types .= "s";
    $params[] = $status;
}
if ($date_from != "") {
    $query .= " AND b.booking_date >= ?";
    $types .= "s";
    $params[] = $date_from;
}
if ($date_to != "") {
    $query .= " AND b.booking_date <= ?";
    $types .= "s";
    $params[] = $date_to;
}
$query .= " ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$results = $stmt->get_result();
?>
<?php include 'includes/header.php'; ?>

<h2>My Booking History</h2>
<form method="GET" class="row g-3 mb-4">
  <div class="col-md-3">
    <input type="date" name="from" class="form-control" value="<?php echo $date_from; ?>">
  </div>
  <div class="col-md-3">
    <input type="date" name="to" class="form-control" value="<?php echo $date_to; ?>">
  </div>
  <div class="col-md-3">
    <select name="status" class="form-select">
      <option value="">All Statuses</option>
      <?php
      $statuses = ['Pending','Confirmed','Checked-in','Completed'];
      foreach ($statuses as $st): ?>
        <option <?php if ($status==$st) echo 'selected'; ?>><?php echo $st; ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <button type="submit" class="btn btn-secondary">Filter</button>
  </div>
</form>

<table class="table table-bordered">
  <thead><tr><th>ID</th><th>Slot</th><th>Date</th><th>Status</th></tr></thead>
  <tbody>
    <?php while ($row = $results->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo $row['booking_date']; ?></td>
        <td><?php echo $row['status']; ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include 'includes/footer.php'; ?>
