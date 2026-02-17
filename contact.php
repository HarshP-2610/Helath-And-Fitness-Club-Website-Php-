<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - FitSoul</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include("navbar.php"); ?>

  <section class="contact-section" style="background:#111;color:#fff;padding:60px 20px;font-family:'Poppins',sans-serif;">
    <div style="max-width:1100px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:28px;">
      <div class="contact-card" style="background:#1a1a1a;border:1px solid rgba(255,215,0,0.25);border-radius:16px;padding:24px;box-shadow:0 8px 24px rgba(0,0,0,0.5);">
        <h2 style="margin-bottom:14px;color:#FFD700;font-size:1.4rem;">Contact Information</h2>
        <p style="color:#ccc;line-height:1.7;margin-bottom:16px;">Reach us through any of the channels below or fill out the form to send a direct message.</p>
        <ul style="list-style:none;padding:0;margin:0;color:#ddd;display:grid;gap:10px;">
          <li><i class="fa-solid fa-location-dot" style="color:#FFD700;margin-right:8px;"></i> 123 Fitness St, Mumbai, India</li>
          <li><i class="fa-solid fa-phone" style="color:#FFD700;margin-right:8px;"></i> +91 98765 43210</li>
          <li><i class="fa-solid fa-envelope" style="color:#FFD700;margin-right:8px;"></i> support@fitsoul.com</li>
          <li style="display:flex;align-items:center;gap:10px;margin-top:6px;">
            <a href="#" style="color:#FFD700;text-decoration:none;"><i class="fab fa-facebook-f"></i></a>
            <a href="#" style="color:#FFD700;text-decoration:none;"><i class="fab fa-instagram"></i></a>
            <a href="#" style="color:#FFD700;text-decoration:none;"><i class="fab fa-twitter"></i></a>
            <a href="#" style="color:#FFD700;text-decoration:none;"><i class="fab fa-youtube"></i></a>
          </li>
        </ul>
      </div>

      <div class="contact-form-card" style="background:#1a1a1a;border:1px solid rgba(255,215,0,0.25);border-radius:16px;padding:24px;box-shadow:0 8px 24px rgba(0,0,0,0.5);">
        <h2 style="margin-bottom:14px;color:#FFD700;font-size:1.4rem;">Send a Message</h2>
        <form id="contactForm" method="post" action="backend/contact_submit.php" style="display:grid;gap:14px;">
          <div style="display:grid;gap:10px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
            <input type="text" name="name" placeholder="Your Name" required style="padding:12px 14px;border-radius:10px;border:1px solid #333;background:#111;color:#fff;">
            <input type="email" name="email" placeholder="Your Email" required style="padding:12px 14px;border-radius:10px;border:1px solid #333;background:#111;color:#fff;">
          </div>
          <input type="text" name="subject" placeholder="Subject" required style="padding:12px 14px;border-radius:10px;border:1px solid #333;background:#111;color:#fff;">
          <textarea name="message" rows="6" placeholder="Your Message" required style="padding:12px 14px;border-radius:10px;border:1px solid #333;background:#111;color:#fff;resize:vertical;"></textarea>
          <button type="submit" class="btn btn-primary" style="justify-content:center;">Send Message</button>
          <div id="formStatus" style="display:none;padding:10px 12px;border-radius:10px;background:#0f5132;color:#d1e7dd;border:1px solid #145a41;"></div>
        </form>
      </div>
    </div>
  </section>

  <script>
    const form = document.getElementById('contactForm');
    const statusBox = document.getElementById('formStatus');
    if (form) {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);
        statusBox.style.display = 'none';
        statusBox.textContent = '';
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        try {
          const res = await fetch(form.action, { method: 'POST', body: formData });
          const data = await res.json().catch(() => ({ ok: false, error: 'Unexpected response' }));
          if (res.ok && data.ok) {
            statusBox.style.background = '#0f5132';
            statusBox.style.color = '#d1e7dd';
            statusBox.style.borderColor = '#145a41';
            statusBox.textContent = data.message || 'Message sent successfully.';
            form.reset();
          } else {
            statusBox.style.background = '#642424';
            statusBox.style.color = '#f8d7da';
            statusBox.style.borderColor = '#7a2a2a';
            statusBox.textContent = (data && data.error) ? data.error : 'Failed to send message.';
          }
        } catch (err) {
          statusBox.style.background = '#642424';
          statusBox.style.color = '#f8d7da';
          statusBox.style.borderColor = '#7a2a2a';
          statusBox.textContent = 'Network error. Please try again.';
        } finally {
          statusBox.style.display = 'block';
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send Message';
        }
      });
    }
  </script>

  <footer class="footer">
    <div class="footer-container">
      <div class="footer-about">
        <h3>FitSOUL</h3>
        <p>Empowering your fitness journey with expert trainers, advanced programs, and a supportive community.</p>
      </div>
      <div class="footer-links">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="membership.php">Membership</a></li>
          <li><a href="locations.php">Locations</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="footer-contact">
        <h4>Contact</h4>
        <p>Email: support@fitsoul.com</p>
        <p>Phone: +91 98765 43210</p>
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


