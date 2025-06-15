<?php
// admin/user_management.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php'); exit;
}
require_once '../includes/db_connect.php';
$message = '';

// Handle user addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    // Check for existing email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $message = "Email already exists.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hash, $role);
        $stmt->execute();
        $message = "User added successfully.";
    }
}

// Fetch users for listing
$users = $conn->query("SELECT id, name, email, role FROM users ORDER BY role, name");
?>
<?php include '../includes/header.php'; ?>

<h2>User Management</h2>
<?php if ($message): ?>
  <div class="alert alert-info"><?php echo $message; ?></div>
<?php endif; ?>

<h4>Add New User</h4>
<form method="POST" action="user_management.php" class="row g-3 mb-4">
  <div class="col-md-3"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
  <div class="col-md-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
  <div class="col-md-3"><input type="text" name="password" class="form-control" placeholder="Password" required></div>
  <div class="col-md-2">
    <select name="role" class="form-select" required>
      <option value="customer">Customer</option>
      <option value="staff">Staff</option>
      <option value="admin">Admin</option>
    </select>
  </div>
  <div class="col-md-1"><button type="submit" class="btn btn-success">Add</button></div>
</form>

<table class="table table-striped">
  <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead>
  <tbody>
    <?php while ($u = $users->fetch_assoc()): ?>
      <tr>
        <td><?php echo $u['id']; ?></td>
        <td><?php echo htmlspecialchars($u['name']); ?></td>
        <td><?php echo htmlspecialchars($u['email']); ?></td>
        <td><?php echo $u['role']; ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include '../includes/footer.php'; ?>
