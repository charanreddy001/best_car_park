<?php
// register.php
require_once 'inc/session.php';
require 'inc/config.php';
require 'inc/functions.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Simple validation
    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already taken.";
            $stmt->close();
        } else {
            $stmt->close();
            // Hash the password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user with role='user'
            $stmt = $conn->prepare("INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $username, $hashed, $fullname);

            if ($stmt->execute()) {
                // Log the registration event
                logAction($conn->insert_id, "Registered new user account");
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<?php include 'inc/header.php'; ?>

<h2>Register</h2>

<?php if (isset($error)): ?>
  <div class="alert alert-danger"><?= htmlentities($error) ?></div>
<?php endif; ?>

<form method="post" class="mt-3">
  <div class="mb-3">
    <label>Username</label>
    <input type="text" name="username" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Full Name</label>
    <input type="text" name="fullname" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Confirm Password</label>
    <input type="password" name="confirm_password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-success">Register</button>
  <a href="login.php" class="btn btn-link">Already have an account? Login</a>
</form>

<?php include 'inc/footer.php'; ?>
