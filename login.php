<?php
// login.php
require_once 'inc/session.php';
require 'inc/config.php';
require 'inc/functions.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Prepare and execute SELECT
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $hash, $role);

    if ($stmt->fetch()) {
        // Close the SELECT statement before calling logAction()
        $stmt->close();

        if (password_verify($password, $hash)) {
            // Log the login event
            logAction($user_id, "Logged in");

            // Set session variables and redirect
            $_SESSION['user_id']  = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role']     = $role;

            if ($role === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
        $stmt->close();
    }
}
?>

<?php include 'inc/header.php'; ?>

<h2>Login</h2>

<?php if (isset($_GET['registered'])): ?>
  <div class="alert alert-success">Registration successful! Please log in.</div>
<?php endif; ?>

<?php if (isset($error)): ?>
  <div class="alert alert-danger"><?= htmlentities($error) ?></div>
<?php endif; ?>

<form method="post" class="mt-3">
  <div class="mb-3">
    <label>Username</label>
    <input type="text" name="username" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Login</button>
  <a href="register.php" class="btn btn-link">Register</a>
</form>

<?php include 'inc/footer.php'; ?>
