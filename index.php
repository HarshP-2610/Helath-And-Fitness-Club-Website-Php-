<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FitSoul Navbar</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include("navbar.php"); ?>

   <section class="hero-section" id="hero">
    <div class="hero-content">
      <div class="hero-badge">
        <i class="fas fa-fire"></i> Transform Your Life
      </div>
      
      <h1 class="hero-title">
        BUILD YOUR <span style="color: #fffc4bff">DREAM BODY</span><br/>
        WITH FITSOUL
      </h1>
      
      <p class="hero-subtitle">
        Join thousands of members achieving their fitness goals with our state-of-the-art facilities, 
        expert trainers, and supportive community.
      </p>
      
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-number">5000+</div>
          <div class="stat-label">Happy Members</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">25</div>
          <div class="stat-label">Expert Trainers</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">98%</div>
          <div class="stat-label">Success Rate</div>
        </div>
      </div>
      
      <div class="cta-buttons">
        <a href="#" id="startTrialLink" class="btn btn-primary">
          <i class="fas fa-dumbbell"></i>
          Start Free Trial
        </a>
        <a href="#" id="virtualTourLink" class="btn btn-secondary">
          <i class="fas fa-video"></i>
          Virtual Tour
        </a>
      </div>
    </div>
    
    <div class="scroll-indicator">
      <div class="scroll-arrow"></div>
    </div>
  </section>

  <script>

    const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;

    window.addEventListener("load", () => {
      document.querySelector(".hero-section").classList.add("visible");
    });


    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener("click", function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute("href"));
        if (target) {
          target.scrollIntoView({ behavior: "smooth" });
        }
      });
    });
    const startTrialLink = document.getElementById('startTrialLink');
    if (startTrialLink) {
      startTrialLink.addEventListener('click', function(e) {
        e.preventDefault();
        if (!isLoggedIn) {
          window.location.href = 'login.php';
          return;
        }
        // open modal
        const code = generateTrialCode(12);
        const codeEl = document.getElementById('trialCode');
        if (codeEl) codeEl.textContent = code;
        const modal = document.getElementById('trialModal');
        if (modal) modal.style.display = 'flex';
      });
    }

    // Virtual Tour button redirect
    const virtualTourLink = document.getElementById('virtualTourLink');
    if (virtualTourLink) {
      virtualTourLink.addEventListener('click', function(e) {
        e.preventDefault();
        window.open('https://youtu.be/tUykoP30Gb0?si=l2phQJjtJKS0Zgt-', '_blank');
      });
    }

    function generateTrialCode(length) {
      const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
      let out = '';
      for (let i = 0; i < length; i++) out += chars[Math.floor(Math.random() * chars.length)];
      return out;
    }

    // modal close handlers
    document.addEventListener('click', function(e) {
      const modal = document.getElementById('trialModal');
      if (!modal) return;
      const isClose = e.target.matches('#trialCloseBtn') || e.target.matches('#trialOkBtn') || e.target === modal;
      if (isClose) modal.style.display = 'none';
    });
  </script>

   <section class="programs-section" id="programs">
    <div class="programs-header">
      <h2>Featured Programs</h2>
      <p>Choose a goal and we’ll guide you every step of the way.</p>
    </div>

    <div class="programs-grid">
      <article class="program-card">
        <div class="program-icon">
          <i class="fa-solid fa-dumbbell" aria-hidden="true"></i>
        </div>
        <h3>Strength Training</h3>
        <p class="program-desc">
          Build lean muscle and increase overall strength with progressive
          overload routines.
        </p>
        <a href="#" class="program-btn">Learn More</a>
      </article>

      <article class="program-card">
        <div class="program-icon">
          <i class="fa-solid fa-fire" aria-hidden="true"></i>
        </div>
        <h3>Fat Loss</h3>
        <p class="program-desc">
          Torch calories with HIIT circuits and metabolic conditioning for
          faster fat loss.
        </p>
        <a href="#" class="program-btn">Learn More</a>
      </article>

      <article class="program-card">
        <div class="program-icon">
          <i class="fa-solid fa-spa" aria-hidden="true"></i>
        </div>
        <h3>Yoga & Flexibility</h3>
        <p class="program-desc">
          Improve mobility, posture, and balance with guided yoga and stretch
          sessions.
        </p>
        <a href="#" class="program-btn">Learn More</a>
      </article>

      <article class="program-card">
        <div class="program-icon">
          <i class="fa-solid fa-person-running" aria-hidden="true"></i>
        </div>
        <h3>CrossFit</h3>
        <p class="program-desc">
          High-intensity functional training to boost endurance, agility, and
          power.
        </p>
        <a href="#" class="program-btn">Learn More</a>
      </article>
    </div>
  </section>

   <section class="special-offers-section">
    <div class="offers-glass">
      <div class="offers-content">
        <h2>🎉 Annual Membership Offer</h2>
        <p>Join now and get <span>20% OFF</span> – Limited time only!</p>
        <a href="membership.php" class="btn-join">Join Now</a>
      </div>
    </div>
    <div class="animated-accent"></div>
  </section>

   <section class="trainers">
    <div class="trainers-header">
      <h2>🏆 Meet Our Expert Trainers</h2>
      <p>Get trained by the best to reach your fitness goals faster.</p>
    </div>

    <div class="trainer-row">
      <div class="trainer-photo">
        <img src="https://media.istockphoto.com/id/1201517718/photo/portrait-of-personal-trainer-in-gym.jpg?s=612x612&w=0&k=20&c=aiUVLyRtOHzKXJ0ocxx-13rz3TMYffcBGULzZxtNpHc=" alt="Alex Johnson">
      </div>
      <div class="trainer-info">
        <h3>Alex Johnson</h3>
        <p>Strength & Conditioning</p>
      </div>
      <div class="trainer-extra">
        <h4>About Alex Johnson</h4>
        <ul>
          <li>5+ Years Experience</li>
          <li>Certified Strength Coach</li>
          <li>Focus on Powerlifting</li>
          <li>Personalized Plans</li>
        </ul>
      </div>
    </div>

    <div class="trainer-row">
      <div class="trainer-photo">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRUww6vyytlmoTKP5oSm399jMguSa1Hp-8Swe2BVOihUN6TWLVkaxKjIsL_uwkuXJYlllM&usqp=CAU" alt="Sophia Lee">
      </div>
      <div class="trainer-info">
        <h3>Sophia Lee</h3>
        <p>Yoga & Flexibility</p>
      </div>
      <div class="trainer-extra">
        <h4>About Sophia Lee</h4>
        <ul>
          <li>10+ Years Yoga Teaching</li>
          <li>Specialist in Flexibility</li>
          <li>Meditation Expert</li>
          <li>Holistic Approach</li>
        </ul>
      </div>
    </div>

    <div class="trainer-row">
      <div class="trainer-photo">
        <img src="https://t3.ftcdn.net/jpg/03/27/48/96/360_F_327489617_OsmQVwhaH1eC445XFW6jZywKnOF2veFA.jpg" alt="Ryan Smith">
      </div>
      <div class="trainer-info">
        <h3>Ryan Smith</h3>
        <p>CrossFit & HIIT</p>
      </div>
      <div class="trainer-extra">
        <h4>About Ryan Smith</h4>
        <ul>
          <li>CrossFit Level 2 Coach</li>
          <li>HIIT Master Trainer</li>
          <li>Group Training Expert</li>
          <li>Fat Loss Specialist</li>
        </ul>
      </div>
    </div>

    <div class="trainer-row">
      <div class="trainer-photo">
        <img src="https://img.freepik.com/premium-photo/young-female-fitness-personal-trainer-with-notepad-standing-gym-with-thumb-up_146671-31563.jpg" alt="Emily Davis">
      </div>
      <div class="trainer-info">
        <h3>Emily Davis</h3>
        <p>Nutrition & Wellness</p>
      </div>
      <div class="trainer-extra">
        <h4>About Emily Davis</h4>
        <ul>
          <li>Certified Nutritionist</li>
          <li>Meal Plan Designer</li>
          <li>Wellness Coaching</li>
          <li>Mind & Body Balance</li>
        </ul>
      </div>
    </div>
  </section>

   <section class="testimonials-section">
    <div class="testimonials-header">
      <h2>Our Trusted Clients</h2>
    </div>

    <div class="testimonials-grid">
      <div class="testimonial-card">
        <p class="testimonial-quote">"The progress tracker is fantastic. I’m motivated to see how much I’ve improved over time."</p>
        <div class="testimonial-user">
          <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Rohan Patel">
          <span>Rohan Patel</span>
        </div>
      </div>

      <div class="testimonial-card">
        <p class="testimonial-quote">"Amazing trainers! The app has a great mix of common and challenging workouts."</p>
        <div class="testimonial-user">
          <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Ananya Sharma">
          <span>Ananya Sharma</span>
        </div>
      </div>

      <div class="testimonial-card">
        <p class="testimonial-quote">"FitSoul helped me transform my body in just 3 months. Highly recommended!"</p>
        <div class="testimonial-user">
          <img src="https://randomuser.me/api/portraits/men/76.jpg" alt="Vikram Singh">
          <span>Vikram Singh</span>
        </div>
      </div>

      <div class="testimonial-card">
        <p class="testimonial-quote">"I feel stronger and healthier than ever. FitSoul is a life-changer!"</p>
        <div class="testimonial-user">
          <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Sneha Mehta">
          <span>Sneha Mehta</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Trial Modal (lightweight, inline styles to avoid global CSS changes) -->
  <div id="trialModal" style="display:none;position:fixed;inset:0;z-index:2000;align-items:center;justify-content:center;background:rgba(0,0,0,0.65)">
    <div style="background:#141414;color:#fff;border:1px solid rgba(255,215,0,0.35);box-shadow:0 10px 30px rgba(255,215,0,0.25);border-radius:16px;max-width:520px;width:92%;padding:20px;text-align:center;position:relative;">
      <button id="trialCloseBtn" aria-label="Close" style="position:absolute;top:8px;right:12px;font-size:26px;background:transparent;border:none;color:#fff;cursor:pointer">&times;</button>
      <h3 style="color:#FFD700;margin-bottom:10px;">Your Free Trial Code</h3>
      <div id="trialCode" style="display:inline-block;padding:12px 16px;border-radius:10px;background:linear-gradient(135deg,#FFD700,#FFC300);color:#111;font-weight:800;letter-spacing:3px;margin-bottom:12px;">—</div>
      <p style="color:#e6e6e6;line-height:1.5;margin-bottom:12px;">Use this code at the front desk or during checkout to redeem a 7‑day free trial. Valid for 24 hours.</p>
      <button id="trialOkBtn" class="btn btn-primary" style="padding:10px 18px;border-radius:8px;">Got it</button>
    </div>
  </div>

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
          <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
          <a href="https://twitter.com/" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>
          <a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      <p>© 2025 FitSOUL. All rights reserved.</p>
    </div>
  </footer>

</body>
</html>
