<?php
// File: admin/manage_feedback.php
require_once '../inc/session.php';
require_once '../inc/config.php';
require_once '../inc/functions.php';

ensureAdmin(); // Ensure only admins can access

// Fetch feedback data
$feedbacks = [];
$sql = "
  SELECT f.id, u.username, f.booking_id, f.rating, f.comment, f.created_at
  FROM feedback f
  LEFT JOIN users u ON f.user_id = u.id
  ORDER BY f.created_at DESC
";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$result->close();

include '../inc/header.php';
?>

<h2 class="mb-4">Manage Feedback</h2>

<table class="table table-striped table-bordered">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Booking ID</th>
      <th>Rating</th>
      <th>Comment</th>
      <th>Submitted At</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($feedbacks as $f): ?>
      <tr>
        <td><?= $f['id'] ?></td>
        <td><?= htmlentities($f['username']) ?></td>
        <td><?= $f['booking_id'] ?></td>
        <td><?= $f['rating'] ?>/5</td>
        <td><?= htmlentities($f['comment']) ?></td>
        <td><?= $f['created_at'] ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($feedbacks)): ?>
      <tr><td colspan="6">No feedback found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include '../inc/footer.php'; ?>
