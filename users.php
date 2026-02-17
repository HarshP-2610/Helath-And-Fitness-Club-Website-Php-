<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include("./backend/db.php");

// Handle delete request (only allow deleting non-admin users)
if (isset($_GET['delete'])) {
  $userId = intval($_GET['delete']);
  // Delete only if the user is not an admin
  $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role <> 'admin'");
  if ($stmt) {
    $stmt->bind_param("i", $userId);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
      $_SESSION['message'] = "User deleted successfully.";
    } else {
      $_SESSION['message'] = "Cannot delete this user (admin or not found).";
    }
    $stmt->close();
  } else {
    $_SESSION['message'] = "Error deleting user: " . $conn->error;
  }
  header("Location: users.php");
  exit;
}

// Fetch all non-admin users
$usersQuery = $conn->query("SELECT id, username, email, mobile, gender, address FROM users WHERE role <> 'admin' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users - FitSoul Admin</title>
  <link rel="stylesheet" href="css/admin.css">
  <style>
  .btn-view-1{
    background: #ffd700;
    color: #000;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    margin-right: 8px;
    transition: transform 0.2s, box-shadow 0.2s;
    display: inline-block;
    border: none;
    cursor: pointer;
    font-size: 14px;
}
</style>
</head>
<body>

<!-- Navbar -->
<?php include("admin_navbar.php"); ?>

<div class="admin-container">
  <section class="admin-section">
    <h2>Manage Users</h2>

    <?php if (isset($_SESSION['message'])): ?>
      <p class="msg"><?= $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Mobile</th>
          <th>Gender</th>
          <th>Address</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($user = $usersQuery->fetch_assoc()): ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= htmlspecialchars($user['username']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= htmlspecialchars($user['mobile']) ?></td>
          <td><?= htmlspecialchars($user['gender']) ?></td>
          <td><?= htmlspecialchars($user['address']) ?></td>
          <td>
            <a href="view_user_plans.php?user_id=<?= $user['id'] ?>" class="btn-view-1">View</a>
            <a href="users.php?delete=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to remove this user?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>
</div>

</body>
</html>
