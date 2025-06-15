<?php
// profile.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); exit;
}
require_once 'includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Fetch current user info
$stmt = $conn->prepare("SELECT name, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    // Verify old password
    if (password_verify($old, $user['password'])) {
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $upd->bind_param("si", $newHash, $user_id);
        $upd->execute();
        $message = "Password updated successfully.";
    } else {
        $message = "Old password is incorrect.";
    }
}
?>
<?php include 'includes/header.php'; ?>

<h2>My Profile</h2>
<?php if ($message): ?>
  <div class="alert alert-info"><?php echo $message; ?></div>
<?php endif; ?>
<ul class="list-group mb-4">
  <li class="list-group-item"><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></li>
  <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
</ul>

<h4>Change Password</h4>
<form method="POST" action="profile.php">
  <div class="mb-3">
    <label>Current Password:</label>
    <input type="password" name="old_password" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>New Password:</label>
    <input type="password" name="new_password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-warning">Update Password</button>
</form>

<?php include 'includes/footer.php'; ?>
