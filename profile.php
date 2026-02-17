<?php
session_start();
include("./backend/db.php");

// Redirect if not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Fetch user details from DB
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id, username, email, mobile, gender, address FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch the user's latest active purchased plan (if any)
$activePlan = null;
if ($user && isset($user['id'])) {
  $userId = intval($user['id']);
  $planStmt = $conn->prepare("SELECT p.title, up.start_date, up.expiry_date, up.price FROM user_plans up JOIN plans p ON up.plan_id = p.id WHERE up.user_id = ? AND up.expiry_date >= NOW() ORDER BY up.expiry_date DESC LIMIT 1");
  if ($planStmt) {
    $planStmt->bind_param('i', $userId);
    $planStmt->execute();
    $planRes = $planStmt->get_result();
    $activePlan = $planRes->fetch_assoc();
    $planStmt->close();
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - FitSOUL</title>
  <link rel="stylesheet" href="css/profile.css">
</head>
<body>
  <?php include("navbar.php"); ?>

  <div class="profile-container">
    <div class="profile-card">
      <h2>👤 My Profile <br> <br></h2>
      <div class="profile-info">
        <p><strong>Full Name &nbsp;&nbsp;&nbsp;:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Mobile    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> <?php echo htmlspecialchars($user['mobile']); ?></p>
        <p><strong>Gender    &nbsp;&nbsp;&nbsp;:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
        <p><strong>Address   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
      </div>
      <?php if ($activePlan): ?>
        <div class="membership-info" style="margin-top:16px;padding:12px;border-top:1px solid #ddd;color:#fff;">
          <h3 style="margin:0 0 8px;">🏷️ Active Membership</h3>
          <p><strong>Plan:</strong> <?php echo htmlspecialchars($activePlan['title']); ?></p>
          <p><strong>Expiry:</strong> <?php echo htmlspecialchars(date('d M Y', strtotime($activePlan['expiry_date']))); ?></p>
          <p><strong>Paid:</strong> ₹ <?php echo htmlspecialchars($activePlan['price']); ?></p>
        </div>
      <?php else: ?>
        <p style="margin-top:12px;color:#FFD700;">You don't have an active membership plan. <a href="membership.php">Choose a plan</a></p>
      <?php endif; ?>
      <div class="profile-actions">
        <a href="edit_profile.php" class="btn">✏️ Edit Profile</a>
        <a href="logout.php" class="btn logout">🚪 Logout</a>
      </div>
    </div>
  </div>
</body>
</html>
