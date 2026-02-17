<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Locations - FitSoul</title>
  <link rel="stylesheet" href="css/style.css"> <!-- Global styles -->
  <link rel="stylesheet" href="css/locations.css"> <!-- Page specific styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include("navbar.php"); ?>

<section class="locations-header">
  <h1>🏋️‍♂️ Our Gym Locations</h1>
  <p>Find a FitSoul branch near you and start your fitness journey today!</p>
</section>

<section class="locations-grid">
  <div class="location-card">
    <i class="fas fa-map-marker-alt"></i>
    <h2>Surat</h2>
    <p>123 Fitness Street, Katargam, Surat</p>
    <p><strong>Phone:</strong> +91 98765 12345</p>
    <a href="https://www.google.com/maps/search/123+Fitness+Street+Katargam+Surat" target="_blank" class="map-btn">View on Map</a>
  </div>

  <div class="location-card">
    <i class="fas fa-map-marker-alt"></i>
    <h2>Ahemdabad</h2>
    <p>45 Gym Avenue, Maninager, Ahemdabad</p>
    <p><strong>Phone:</strong> +91 98765 67890</p>
    <a href="https://www.google.com/maps/search/45+Gym+Avenue+Maninager+Ahmedabad" target="_blank" class="map-btn">View on Map</a>
  </div>

  <div class="location-card">
    <i class="fas fa-map-marker-alt"></i>
    <h2>Rajkot</h2>
    <p>78 Health Road, Gondal, Rajkot</p>
    <p><strong>Phone:</strong> +91 99887 66554</p>
    <a href="https://www.google.com/maps/search/78+Health+Road+Gondal+Rajkot" target="_blank" class="map-btn">View on Map</a>
  </div>
</section>

<footer class="footer" id="about">
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
