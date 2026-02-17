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
   <link rel="stylesheet" href="css/style.css">
    <title>Navbar</title>
</head>
<body>
    <nav class="navbar">
      <div class="navbar-container">
        <!-- Logo -->
        <div class="nav-logo">FitSoul</div>

        <div class="nav-menu">
          <ul class="nav-links" id="navLinks">
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#programs">Services</a></li>
            <li><a href="membership.php">Membership Plans</a></li>
            <li><a href="locations.php">Locations</a></li>
            <li><a href="index.php#about">About Us</a></li>
            <li><a href="contact.php">Contact Us</a></li>

            <?php if (isset($_SESSION['username'])): ?>
              <!-- Username now links to profile page -->
              <li><a href="profile.php">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
              <li><a href="logout.php" class="logout-btn">Logout</a></li>
            <?php else: ?>
              <li><a href="login.php" class="login-btn">Login</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>

    <script>
      // Mobile menu toggle (if you add a menu icon later)
      const menuIcon = document.getElementById("menuIcon");
      const navLinks = document.getElementById("navLinks");

      if (menuIcon) {
        menuIcon.addEventListener("click", () => {
          navLinks.classList.toggle("open");
          menuIcon.classList.toggle("active");
        });
      }
    </script>
</body>
</html>
