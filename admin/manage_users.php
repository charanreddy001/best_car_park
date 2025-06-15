<?php
// File: admin/manage_users.php
require_once '../inc/session.php';
require_once '../inc/config.php';
require_once '../inc/functions.php';

// Only allow admins here
ensureAdmin();

// Handle role update
if (isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = ($_POST['role'] === 'admin') ? 'admin' : 'user';
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
    exit;
}

// Handle user deletion (except self)
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    if ($user_id !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_users.php");
    exit;
}

// Fetch all users
$users = [];
$res = $conn->query("SELECT id, username, fullname, role FROM users ORDER BY id ASC");
while ($row = $res->fetch_assoc()) {
    $users[] = $row;
}
$res->close();
?>

<?php include '../inc/header.php'; ?>

<h2 class="mb-4">Manage Users</h2>
<table class="table table-striped table-bordered align-middle">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Full Name</th>
      <th>Role</th>
      <th style="width: 25%;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlentities($u['username']) ?></td>
        <td><?= htmlentities($u['fullname']) ?></td>
        <td><?= $u['role'] ?></td>
        <td>
          <div class="d-flex flex-wrap gap-2">
            <!-- Update Role Form -->
            <form method="post" class="d-flex gap-2 align-items-center">
              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
              <select name="role" class="form-select form-select-sm">
                <option value="user" <?= ($u['role'] === 'user') ? 'selected' : '' ?>>user</option>
                <option value="admin" <?= ($u['role'] === 'admin') ? 'selected' : '' ?>>admin</option>
              </select>
              <button type="submit" name="update_role" class="btn btn-sm btn-primary">Update</button>
            </form>

            <!-- Delete User Form (cannot delete self) -->
            <?php if ($u['id'] !== $_SESSION['user_id']): ?>
              <form method="post" class="d-inline" onsubmit="return confirm('Delete user <?= htmlentities($u['username']) ?>?');">
                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                <button type="submit" name="delete_user" class="btn btn-sm btn-danger">Delete</button>
              </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include '../inc/footer.php'; ?>
