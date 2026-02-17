<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css"> <!-- ✅ reuse same theme -->
  <title>Admin Navbar</title>
</head>
<body>
  <nav class="navbar">
    <div class="navbar-container">
      <!-- Logo -->
      <div class="nav-logo">FitSoul Admin</div>

      <div class="nav-menu">
        <ul class="nav-links" id="navLinks">
          <li><a href="admin.php#dashboard">Dashboard</a></li>
          <li><a href="plans.php">Manage Plans</a></li>
          <li><a href="users.php">Manage Users</a></li>
          <li><a href="admin.php#contacts">Manage Contacts</a></li>

          <?php if (isset($_SESSION['username'])): ?>
            <li><a href="#">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</body>
</html>
