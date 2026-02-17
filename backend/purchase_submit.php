<?php
session_start();
require_once __DIR__ . '/db.php';

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
  header('Location: ../login.php');
  exit;
}

if (!isset($_POST['plan_id'])) {
  header('Location: ../membership.php');
  exit;
}

$plan_id = intval($_POST['plan_id']);
$email = $_SESSION['email'];

// Get user id
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
  $stmt->close();
  header('Location: ../login.php');
  exit;
}
$user = $res->fetch_assoc();
$user_id = $user['id'];
$stmt->close();

// Fetch plan details
$stmt = $conn->prepare("SELECT id, validity, price FROM plans WHERE id = ?");
$stmt->bind_param('i', $plan_id);
$stmt->execute();
$plan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$plan) {
  header('Location: ../membership.php');
  exit;
}

// Create user_plans table if it doesn't exist
$create = "CREATE TABLE IF NOT EXISTS user_plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  plan_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  start_date DATETIME NOT NULL,
  expiry_date DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id),
  INDEX (plan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($create);

// Compute start and expiry dates
$start = new DateTime();
$expiry = new DateTime();
$expiry->modify('+' . intval($plan['validity']) . ' months');
$start_s = $start->format('Y-m-d H:i:s');
$expiry_s = $expiry->format('Y-m-d H:i:s');

// Insert purchase record
$stmt = $conn->prepare("INSERT INTO user_plans (user_id, plan_id, price, start_date, expiry_date) VALUES (?, ?, ?, ?, ?)");
if ($stmt) {
  // Ensure price is numeric and bind dates as strings
  $price = floatval($plan['price']);
  $stmt->bind_param('iidss', $user_id, $plan_id, $price, $start_s, $expiry_s);
  $stmt->execute();
  $stmt->close();
}

$conn->close();

// Redirect back to profile
header('Location: ../profile.php?purchase=success');
exit;

?>
