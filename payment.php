<?php
session_start();
require_once __DIR__ . '/backend/db.php';

if (!isset($_SESSION['username']) || !isset($_GET['plan_id'])) {
  header('Location: membership.php');
  exit;
}

$planId = intval($_GET['plan_id']);
$stmt = $conn->prepare('SELECT id, title, description, validity, price FROM plans WHERE id = ?');
$stmt->bind_param('i', $planId);
$stmt->execute();
$plan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$plan) {
  header('Location: membership.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - <?= htmlspecialchars($plan['title']) ?></title>
  <link rel="stylesheet" href="css/membership.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .payment-tabs {
      display: flex;
      margin-bottom: 20px;
      border-bottom: 2px solid #333;
    }
    
    .tab-btn {
      flex: 1;
      padding: 12px 20px;
      background: transparent;
      border: none;
      color: #888;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s ease;
      border-bottom: 2px solid transparent;
    }
    
    .tab-btn:hover {
      color: #FFD700;
      background: rgba(255, 215, 0, 0.1);
    }
    
    .tab-btn.active {
      color: #FFD700;
      border-bottom-color: #FFD700;
      background: rgba(255, 215, 0, 0.1);
    }
    
    .payment-form {
      display: none;
    }
    
    .payment-form.active {
      display: block;
    }
    
    .upi-input-group {
      margin-bottom: 15px;
    }
    
    .upi-input-group label {
      display: block;
      margin-bottom: 5px;
    }
    
    .upi-input-group small {
      display: block;
      margin-top: 5px;
    }
    
    .upi-apps {
      margin: 20px 0;
    }
    
    .upi-app-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-top: 10px;
    }
    
    .upi-app-option {
      cursor: pointer;
    }
    
    .upi-app-option input[type="radio"] {
      display: none;
    }
    
    .upi-app-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 15px 10px;
      border: 2px solid #333;
      border-radius: 10px;
      background: #1a1a1a;
      transition: all 0.3s ease;
      text-align: center;
    }
    
    .upi-app-card i {
      font-size: 24px;
      margin-bottom: 8px;
    }
    
    .upi-app-card span {
      font-size: 12px;
      color: #ccc;
      font-weight: 500;
    }
    
    .upi-app-option input[type="radio"]:checked + .upi-app-card {
      border-color: #FFD700;
      background: rgba(255, 215, 0, 0.1);
      transform: scale(1.02);
    }
    
    .upi-app-option:hover .upi-app-card {
      border-color: #FFD700;
      background: rgba(255, 215, 0, 0.05);
    }
    
    .upi-btn {
      background: linear-gradient(135deg, #5f259f, #7c3aed);
      border: none;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      margin-top: 15px;
    }
    
    .upi-btn:hover {
      background: linear-gradient(135deg, #7c3aed, #5f259f);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(95, 37, 159, 0.3);
    }
    
    .upi-btn i {
      margin-right: 8px;
    }
    
    @media (max-width: 768px) {
      .upi-app-grid {
        grid-template-columns: 1fr;
      }
      
      .plans-container {
        grid-template-columns: 1fr !important;
      }
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>

  <!-- Hidden purchase form submitted after successful payment -->
  <form id="purchaseForm" method="post" action="backend/purchase_submit.php" style="display:none;">
    <input type="hidden" name="plan_id" value="<?= $planId ?>">
  </form>

  <section class="plans-section" style="padding-top:40px;">
    <div class="plans-header">
      <h1>Checkout</h1>
      <p>Complete your purchase to activate your membership.</p>
    </div>

    <div class="plans-container" style="display:grid;grid-template-columns:1fr 1fr;gap:24px;max-width:1000px;">
      <div class="plan-card">
        <h2><?= htmlspecialchars($plan['title']) ?></h2>
        <p class="plan-description"><?= htmlspecialchars($plan['description']) ?></p>
        <p class="plan-validity"><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($plan['validity']) ?> Months</p>
        <p class="plan-price">₹ <?= htmlspecialchars($plan['price']) ?></p>
      </div>

      <div class="plan-card">
        <h2>Payment Details</h2>
        
        <!-- Payment Method Tabs -->
        <div class="payment-tabs">
          <button class="tab-btn active" onclick="switchTab('card')">
            <i class="fas fa-credit-card"></i> Card Payment
          </button>
          <button class="tab-btn" onclick="switchTab('upi')">
            <i class="fas fa-mobile-alt"></i> UPI Payment
          </button>
        </div>

        <!-- Card Payment Form -->
        <div id="card-payment" class="payment-form active">
          <form method="post" action="" onsubmit="event.preventDefault(); simulateCardPay();">
            <div style="display:grid;gap:10px;">
              <input type="text" placeholder="Name on Card" required style="padding:10px;border-radius:8px;border:1px solid #333;background:#111;color:#fff;">
              <input type="text" placeholder="Card Number" required maxlength="19" style="padding:10px;border-radius:8px;border:1px solid #333;background:#111;color:#fff;">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <input type="text" placeholder="MM/YY" required maxlength="5" style="padding:10px;border-radius:8px;border:1px solid #333;background:#111;color:#fff;">
                <input type="password" placeholder="CVV" required maxlength="4" style="padding:10px;border-radius:8px;border:1px solid #333;background:#111;color:#fff;">
              </div>
              <button type="submit" class="btn-join">
                <i class="fas fa-credit-card"></i> Pay ₹ <?= htmlspecialchars($plan['price']) ?>
              </button>
            </div>
          </form>
        </div>

        <!-- UPI Payment Form -->
        <div id="upi-payment" class="payment-form">
          <form method="post" action="" onsubmit="event.preventDefault(); simulateUPIPay();">
            <div style="display:grid;gap:10px;">
              <div class="upi-input-group">
                <label style="color:#FFD700;font-size:14px;margin-bottom:5px;">UPI ID</label>
                <input type="text" id="upiId" placeholder="yourname@paytm" required style="padding:10px;border-radius:8px;border:1px solid #333;background:#111;color:#fff;">
                <small style="color:#888;font-size:12px;">Enter your UPI ID (e.g., yourname@paytm, yourname@phonepe)</small>
              </div>
              
              <div class="upi-apps">
                <p style="color:#FFD700;font-size:14px;margin:15px 0 10px;">Choose UPI App:</p>
                <div class="upi-app-grid">
                  <label class="upi-app-option">
                    <input type="radio" name="upiApp" value="phonepe" checked>
                    <div class="upi-app-card">
                      <i class="fas fa-mobile-alt" style="color:#5f259f;"></i>
                      <span>PhonePe</span>
                    </div>
                  </label>
                  <label class="upi-app-option">
                    <input type="radio" name="upiApp" value="paytm">
                    <div class="upi-app-card">
                      <i class="fas fa-wallet" style="color:#00baf2;"></i>
                      <span>Paytm</span>
                    </div>
                  </label>
                  <label class="upi-app-option">
                    <input type="radio" name="upiApp" value="gpay">
                    <div class="upi-app-card">
                      <i class="fab fa-google-pay" style="color:#4285f4;"></i>
                      <span>Google Pay</span>
                    </div>
                  </label>
                  <label class="upi-app-option">
                    <input type="radio" name="upiApp" value="bharatpe">
                    <div class="upi-app-card">
                      <i class="fas fa-qrcode" style="color:#00d4aa;"></i>
                      <span>BharatPe</span>
                    </div>
                  </label>
                </div>
              </div>
              
              <button type="submit" class="btn-join upi-btn">
                <i class="fas fa-mobile-alt"></i> Pay with UPI ₹ <?= htmlspecialchars($plan['price']) ?>
              </button>
            </div>
          </form>
        </div>
        
        <p id="payStatus" style="display:none;margin-top:10px;color:#FFD700;">Processing payment...</p>
      </div>
    </div>
  </section>

  <script>
    // Tab switching functionality
    function switchTab(tabName) {
      // Hide all payment forms
      document.querySelectorAll('.payment-form').forEach(form => {
        form.classList.remove('active');
      });
      
      // Remove active class from all tabs
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Show selected form and activate tab
      document.getElementById(tabName + '-payment').classList.add('active');
      event.target.classList.add('active');
    }

    // Card payment simulation
    function simulateCardPay() {
      const status = document.getElementById('payStatus');
      status.style.display = 'block';
      status.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing card payment...';

      setTimeout(() => {
        status.innerHTML = '<i class="fas fa-check-circle"></i> Card payment successful!';
        setTimeout(() => {
          // Record purchase server-side then redirect to profile
          submitPurchase().then(() => {
            alert('Payment successful! Welcome to <?= htmlspecialchars(addslashes($plan['title'])) ?>.');
            window.location.href = 'profile.php?purchase=success';
          }).catch(() => {
            alert('Payment succeeded but could not record purchase. Please contact support.');
            window.location.href = 'profile.php';
          });
        }, 800);
      }, 1400);
    }

    // UPI payment simulation
    function simulateUPIPay() {
      const upiId = document.getElementById('upiId').value;
      const selectedApp = document.querySelector('input[name="upiApp"]:checked').value;
      const status = document.getElementById('payStatus');

      // Validate UPI ID format
      if (!isValidUPIId(upiId)) {
        alert('Please enter a valid UPI ID (e.g., yourname@paytm, yourname@phonepe)');
        return;
      }

      status.style.display = 'block';
      status.innerHTML = '<i class="fas fa-mobile-alt"></i> Opening UPI app...';

      setTimeout(() => {
        status.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing UPI payment...';

        setTimeout(() => {
          status.innerHTML = '<i class="fas fa-check-circle"></i> UPI payment successful!';
          setTimeout(() => {
            // Record purchase server-side then redirect to profile
            submitPurchase().then(() => {
              alert(`Payment successful via ${selectedApp.toUpperCase()}! Welcome to <?= htmlspecialchars(addslashes($plan['title'])) ?>.`);
              window.location.href = 'profile.php?purchase=success';
            }).catch(() => {
              alert('Payment succeeded but could not record purchase. Please contact support.');
              window.location.href = 'profile.php';
            });
          }, 800);
        }, 1400);
      }, 900);
    }

    // Submit the hidden purchase form to the backend to record the plan
    function submitPurchase() {
      return new Promise((resolve, reject) => {
        const form = document.getElementById('purchaseForm');
        const action = form.action;
        const data = new FormData(form);

        fetch(action, {
          method: 'POST',
          body: data,
          credentials: 'same-origin'
        }).then(response => {
          if (response.redirected || response.ok) {
            resolve();
          } else {
            reject();
          }
        }).catch(err => {
          reject(err);
        });
      });
    }

    // UPI ID validation
    function isValidUPIId(upiId) {
      const upiRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+$/;
      return upiRegex.test(upiId);
    }

    // UPI ID input formatting
    document.getElementById('upiId').addEventListener('input', function(e) {
      let value = e.target.value.toLowerCase();
      // Auto-suggest common UPI providers
      if (value.includes('@') && !value.includes('@paytm') && !value.includes('@phonepe') && 
          !value.includes('@gpay') && !value.includes('@bharatpe') && !value.includes('@ybl')) {
        // You can add auto-complete suggestions here
      }
      e.target.value = value;
    });

    // Auto-format card number
    document.querySelector('input[placeholder="Card Number"]').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
      let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
      e.target.value = formattedValue;
    });

    // Auto-format expiry date
    document.querySelector('input[placeholder="MM/YY"]').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
      }
      e.target.value = value;
    });
  </script>
</body>
</html>


