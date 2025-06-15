<?php
require_once '../inc/session.php';
require_once '../inc/config.php';
require_once '../inc/functions.php';

ensureAdmin(); // Only allow admin access

// Fetch audit log data
$logs = [];
$sql = "
  SELECT l.id, u.username, l.action, l.timestamp
  FROM audit_logs l
  LEFT JOIN users u ON l.user_id = u.id
  ORDER BY l.timestamp DESC
";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}
$result->close();

include '../inc/header.php';
?>

<h2 class="mb-4">Audit Logs</h2>

<table class="table table-striped table-bordered">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Action</th>
      <th>Timestamp</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($logs as $log): ?>
      <tr>
        <td><?= $log['id'] ?></td>
        <td><?= htmlentities($log['username']) ?></td>
        <td><?= htmlentities($log['action']) ?></td>
        <td><?= $log['timestamp'] ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($logs)): ?>
      <tr><td colspan="4">No logs found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include '../inc/footer.php'; ?>
