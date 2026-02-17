<?php
session_start();
include("./backend/db.php");

// Fetch all plans from DB
$plansQuery = $conn->query("SELECT * FROM plans ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Membership Plans - FitSoul</title>
  <link rel="stylesheet" href="css/membership.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

  <!-- Navbar -->
  <?php include("navbar.php"); ?>

  <section class="plans-section">
    <div class="plans-header">
      <h1>💪 Membership Plans</h1>
      <p>Choose the best plan that suits your fitness journey.</p>
    </div>

    <div class="plans-container">
      <?php while ($plan = $plansQuery->fetch_assoc()): ?>
        <div class="plan-card" style="position:relative;">
          <?php if (stripos($plan['title'], 'annual') !== false || stripos($plan['title'], 'year') !== false): ?>
            <div style="position:absolute;top:-8px;right:-8px;background:linear-gradient(135deg,#FFD700,#FFC300);color:#111;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:0.8rem;box-shadow:0 4px 12px rgba(255,215,0,0.4);z-index:10;">
              20% OFF
            </div>
          <?php endif; ?>
          <h2><?= htmlspecialchars($plan['title']); ?></h2>
          <p class="plan-description"><?= htmlspecialchars($plan['description']); ?></p>
          <p class="plan-validity"><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($plan['validity']); ?> Months</p>
          <p class="plan-price">₹ <?= htmlspecialchars($plan['price']); ?></p>
          <a href="#" class="btn-join" data-plan-id="<?= htmlspecialchars($plan['id']); ?>">Join Now</a>
        </div>
      <?php endwhile; ?>
    </div>
  </section>

  <script>
    const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-join');
      if (!btn) return;
      e.preventDefault();
      const planId = btn.getAttribute('data-plan-id');
      if (!isLoggedIn) {
        window.location.href = 'login.php';
        return;
      }
      window.location.href = 'payment.php?plan_id=' + encodeURIComponent(planId);
    });
  </script>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">

      <div class="footer-about">
        <h3>FitSOUL</h3>
        <p>
          Empowering your fitness journey with expert trainers, advanced programs, and a community that motivates you to reach your goals.
        </p>
      </div>

      <div class="footer-links">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="/">Home</a></li>
          <li><a href="/trainers">Trainers</a></li>
          <li><a href="/programs">Programs</a></li>
          <li><a href="/contact">Contact</a></li>
          <li><a href="/login">Login</a></li>
        </ul>
      </div>

      <div class="footer-contact">
        <h4>Contact</h4>
        <p>Email: support@fitsoul.com</p>
        <p>Phone: +91 98765 43210</p>
        <p>Address: 123 Fitness St, Mumbai, India</p>
      </div>

      <div class="footer-social">
        <h4>Follow Us</h4>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      <p>© 2025 FitSOUL. All rights reserved.</p>
    </div>
  </footer>

</body>
</html>
