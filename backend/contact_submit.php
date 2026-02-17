<?php
// Ensure no BOM/whitespace before PHP
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

// Create table if not exists
$createTableSql = "CREATE TABLE IF NOT EXISTS contact_us (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($createTableSql)) {
  http_response_code(500);
  echo json_encode([ 'ok' => false, 'error' => 'Failed to ensure table: ' . $conn->error ]);
  exit;
}

// Validate method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode([ 'ok' => false, 'error' => 'Method not allowed' ]);
  exit;
}

// Collect and validate input
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($name === '' || $email === '' || $subject === '' || $message === '') {
  http_response_code(422);
  echo json_encode([ 'ok' => false, 'error' => 'All fields are required.' ]);
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(422);
  echo json_encode([ 'ok' => false, 'error' => 'Invalid email address.' ]);
  exit;
}

// Insert using prepared statement
$stmt = $conn->prepare("INSERT INTO contact_us (name, email, subject, message) VALUES (?, ?, ?, ?)");
if (!$stmt) {
  http_response_code(500);
  echo json_encode([ 'ok' => false, 'error' => 'Prepare failed: ' . $conn->error ]);
  exit;
}

$stmt->bind_param('ssss', $name, $email, $subject, $message);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode([ 'ok' => false, 'error' => 'Execute failed: ' . $stmt->error ]);
  $stmt->close();
  exit;
}

$stmt->close();

echo json_encode([ 'ok' => true, 'message' => 'Thanks! Your message has been sent.' ]);
exit;




