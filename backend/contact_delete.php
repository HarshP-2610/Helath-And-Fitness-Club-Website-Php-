<?php
// No BOM
session_start();

require_once __DIR__ . '/db.php';

// Only admins can delete
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? null) !== 'admin') {
    http_response_code(302);
    header('Location: ../login.php');
    exit;
}

// CSRF check
if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf'])) {
    http_response_code(400);
    echo 'Invalid CSRF token';
    exit;
}

// Validate ID
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo 'Invalid ID';
    exit;
}

$stmt = $conn->prepare('DELETE FROM contact_us WHERE id = ?');
if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: ../admin.php#contacts');
exit;
?>


