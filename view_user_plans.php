<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include("./backend/db.php");

// Get user ID from query parameter
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($userId <= 0) {
    header("Location: users.php");
    exit;
}

// Fetch user info (include role to prevent viewing admin)
$userStmt = $conn->prepare("SELECT id, username, email, mobile, gender, address, role FROM users WHERE id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
$userStmt->close();

if (!$user) {
  header("Location: users.php");
  exit;
}

// Do not allow viewing admin accounts here
if (isset($user['role']) && $user['role'] === 'admin') {
  $_SESSION['message'] = 'Cannot view admin accounts.';
  header("Location: users.php");
  exit;
}

// Fetch purchased plans for this user
$plansStmt = $conn->prepare(
    "SELECT up.id, p.title, up.price, up.start_date, up.expiry_date, up.created_at 
     FROM user_plans up 
     JOIN plans p ON up.plan_id = p.id 
     WHERE up.user_id = ? 
     ORDER BY up.created_at DESC"
);
$plansStmt->bind_param("i", $userId);
$plansStmt->execute();
$plansResult = $plansStmt->get_result();
$plansStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Purchased Plans - FitSoul Admin</title>
  <link rel="stylesheet" href="css/admin.css">
  <style>
    .user-details {
      background: #111;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .user-details h3 { margin-bottom: 15px; color: #fff; }
    .user-details p { margin: 8px 0; color: #fff; }
    .user-details strong { color: #fff; }
    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 15px;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.3s;
    }
    .back-link:hover { background: #0056b3; }
    .no-plans {
      background: #fffbea;
      padding: 20px;
      border-left: 4px solid #ff9800;
      border-radius: 4px;
      color: #ff6f00;
    }
    table tbody tr:nth-child(even) { background: #f9f9f9; }
    /* table tbody tr:hover { background: #f0f0f0; } */
  </style>
</head>
<body>

<!-- Navbar -->
<?php include("admin_navbar.php"); ?>

<div class="admin-container">
  <section class="admin-section">
    <a href="users.php" class="back-link">← Back to Users</a>
    
    <h2>Purchased Plans for <?= htmlspecialchars($user['username']) ?></h2>

    <!-- User Details -->
    <div class="user-details">
      <h3>User Information</h3>
      <p><strong>Username &nbsp:</strong> <?= htmlspecialchars($user['username']) ?></p>
      <p><strong>Email &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp:</strong> <?= htmlspecialchars($user['email']) ?></p>
      <p><strong>Mobile &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp:</strong> <?= htmlspecialchars($user['mobile']) ?></p>
      <p><strong>Gender &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp:</strong> <?= htmlspecialchars($user['gender']) ?></p>
      <p><strong>Address &nbsp&nbsp&nbsp&nbsp&nbsp:</strong> <?= htmlspecialchars($user['address']) ?></p>
    </div>

    <!-- Purchased Plans Table -->
    <?php if ($plansResult->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Plan Name</th>
            <th>Price</th>
            <th>Start Date</th>
            <th>Expiry Date</th>
            <th>Purchased On</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($plan = $plansResult->fetch_assoc()): ?>
          <tr>
            <td><?= $plan['id'] ?></td>
            <td><?= htmlspecialchars($plan['title']) ?></td>
            <td>$<?= number_format($plan['price'], 2) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($plan['start_date'])) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($plan['expiry_date'])) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($plan['created_at'])) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="no-plans">
        <strong>No purchased plans found for this user.</strong>
      </div>
    <?php endif; ?>

  </section>
</div>

</body>
</html>
