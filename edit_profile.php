<?php
session_start();
require_once __DIR__ . '/backend/db.php';

// Must be logged in
if (!isset($_SESSION['email'])) {
  header('Location: login.php');
  exit;
}

$email = $_SESSION['email'];
$message = '';

// Handle update on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = isset($_POST['username']) ? trim($_POST['username']) : '';
  $mobile   = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
  $gender   = isset($_POST['gender']) ? trim($_POST['gender']) : '';
  $address  = isset($_POST['address']) ? trim($_POST['address']) : '';

  if ($username === '' || $mobile === '' || $gender === '' || $address === '') {
    $message = 'All fields are required.';
  } else {
    $stmt = $conn->prepare('UPDATE users SET username = ?, mobile = ?, gender = ?, address = ? WHERE email = ?');
    if ($stmt) {
      $stmt->bind_param('sssss', $username, $mobile, $gender, $address, $email);
      if ($stmt->execute()) {
        // Update session mirror values
        $_SESSION['username'] = $username;
        $_SESSION['mobile']   = $mobile;
        $_SESSION['gender']   = $gender;
        $_SESSION['address']  = $address;
        header('Location: profile.php');
        exit;
      } else {
        $message = 'Failed to update. Please try again.';
      }
      $stmt->close();
    } else {
      $message = 'Server error. Please try later.';
    }
  }
}

// Fetch current values for form
$stmt = $conn->prepare('SELECT username, email, mobile, gender, address FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile - FitSOUL</title>
  <link rel="stylesheet" href="css/profile.css">
</head>
<body>
  <?php include 'navbar.php'; ?>

  <div class="profile-container">
    <div class="profile-card">
      <h2>✏️ Edit Profile</h2>
      <?php if ($message !== ''): ?>
        <p style="background:#642424;color:#f8d7da;border:1px solid #7a2a2a;padding:10px 12px;border-radius:10px;margin:12px 0;">
          <?php echo htmlspecialchars($message); ?>
        </p>
      <?php endif; ?>

      <form method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:15px 20px;">
        <div>
          <label style="display:block;color:#FFD700;margin-bottom:6px;">Full Name</label>
          <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #333;background:#111;color:#fff;" required>
        </div>
        <div>
          <label style="display:block;color:#FFD700;margin-bottom:6px;">Email</label>
          <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled style="width:100%;padding:10px;border-radius:8px;border:1px solid #333;background:#222;color:#999;">
        </div>
        <div>
          <label style="display:block;color:#FFD700;margin-bottom:6px;">Mobile</label>
          <input type="text" name="mobile" value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #333;background:#111;color:#fff;" required>
        </div>
        <div>
          <label style="display:block;color:#FFD700;margin-bottom:6px;">Gender</label>
          <select name="gender" style="width:100%;padding:10px;border-radius:8px;border:1px solid #333;background:#111;color:#fff;" required>
            <option value="Male" <?php echo (($user['gender'] ?? '')==='Male')?'selected':''; ?>>Male</option>
            <option value="Female" <?php echo (($user['gender'] ?? '')==='Female')?'selected':''; ?>>Female</option>
            <option value="Other" <?php echo (($user['gender'] ?? '')==='Other')?'selected':''; ?>>Other</option>
          </select>
        </div>
        <div style="grid-column:1 / -1;">
          <label style="display:block;color:#FFD700;margin-bottom:6px;">Address</label>
          <textarea name="address" rows="3" style="width:100%;padding:10px;border-radius:8px;border:1px solid #333;background:#111;color:#fff;resize:vertical;" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </div>
        <div style="grid-column:1 / -1;display:flex;gap:10px;justify-content:flex-end;">
          <button type="submit" class="btn" style="background:#FFD700;color:#111;">Save Changes</button>
          <a href="profile.php" class="btn" style="background:#333;color:#fff;">Cancel</a>
        </div>
      </form>
    </div>
  </div>

</body>
</html>


