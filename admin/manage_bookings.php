<?php
// File: admin/manage_bookings.php
require_once '../inc/session.php';
require_once '../inc/config.php';
require_once '../inc/functions.php';

// Only allow admins here
ensureAdmin();

// Fetch all bookings joined only to users (no start_time/end_time)
$bookings = [];
$sql = "
  SELECT
    b.id         AS booking_id,
    u.username   AS user_name,
    b.vehicle_id AS vehicle_id,
    b.slot_id    AS slot_id
  FROM bookings b
  LEFT JOIN users u ON b.user_id = u.id
  ORDER BY b.id ASC
";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $bookings[] = $row;
}
$res->close();
?>

<?php include '../inc/header.php'; ?>

<h2 class="mb-4">Manage Bookings</h2>
<table class="table table-striped table-bordered align-middle">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Vehicle ID</th>
      <th>Slot ID</th>
      <!-- If you want deletion, uncomment the next column -->
      <!-- <th>Actions</th> -->
    </tr>
  </thead>
  <tbody>
    <?php foreach ($bookings as $b): ?>
      <tr>
        <td><?= $b['booking_id'] ?></td>
        <td><?= htmlentities($b['user_name']) ?></td>
        <td><?= htmlentities($b['vehicle_id']) ?></td>
        <td><?= htmlentities($b['slot_id']) ?></td>
        <!--
        <td>
          <form method="post" onsubmit="return confirm('Delete booking #<?= $b['booking_id'] ?>?');">
            <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
            <button type="submit" name="delete_booking" class="btn btn-sm btn-danger">Delete</button>
          </form>
        </td>
        -->
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include '../inc/footer.php'; ?>
