<?php
// index.php (Login page)
session_start();
require_once 'includes/db_connect.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection:contentReference[oaicite:12]{index=12}
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify password hash
        if (password_verify($password, $row['password'])) {
            // Credentials are correct; set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            // Redirect based on role
            if ($row['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } elseif ($row['role'] === 'staff') {
                header('Location: staff/staff_panel.php');
            } else {
                header('Location: booking.php');
            }
            exit;
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "Invalid email or password.";
    }
}
?>
<?php include 'includes/header.php'; ?>

<h2>Login</h2>
<?php if ($message): ?>
  <div class="alert alert-danger"><?php echo $message; ?></div>
<?php endif; ?>
<form method="POST" action="index.php">
  <div class="mb-3">
    <label>Email:</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Password:</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Login</button>
</form>

<?php include 'includes/footer.php'; ?>
