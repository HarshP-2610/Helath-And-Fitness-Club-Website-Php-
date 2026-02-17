<?php
session_start();

// Only admin can view this
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/backend/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT id, name, email, subject, message, created_at FROM contact_us WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    $stmt->close();
    header('Location: admin.php');
    exit;
}
$contact = $res->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>View Contact - Admin</title>
  <link rel="stylesheet" href="css/admin.css">
  <style>
    .contact-view { max-width:900px;margin:28px auto;padding:20px;background:#111;border-radius:8px;color:#fff; }
    .contact-row { margin-bottom:12px; }
    .contact-label { color:#FFD700;font-weight:700;margin-right:8px; }
    .actions { margin-top:18px; }
  </style>
</head>
<body>
<?php include 'admin_navbar.php'; ?>

<div class="contact-view">
  <h2>Contact Message</h2>
  <div class="contact-row"><span class="contact-label">ID:</span> <?= htmlspecialchars($contact['id']) ?></div>
  <div class="contact-row"><span class="contact-label">Name:</span> <?= htmlspecialchars($contact['name']) ?></div>
  <div class="contact-row"><span class="contact-label">Email:</span> <?= htmlspecialchars($contact['email']) ?></div>
  <div class="contact-row"><span class="contact-label">Subject:</span> <?= htmlspecialchars($contact['subject']) ?></div>
  <div class="contact-row"><span class="contact-label">Received:</span> <?= htmlspecialchars($contact['created_at']) ?></div>
  <hr style="border-color:#333;margin:16px 0;">
  <div class="contact-row"><span class="contact-label">Message:</span>
    <div style="white-space:pre-wrap;margin-top:8px;padding:12px;background:#0d0d0d;border-radius:6px;"><?= nl2br(htmlspecialchars($contact['message'])) ?></div>
  </div>

  <div class="actions">
    <a href="admin.php" class="btn" style="margin-right:8px;">Back to Admin</a>

    <form method="post" action="backend/contact_delete.php" onsubmit="return confirm('Delete this message?');" style="display:inline;">
      <input type="hidden" name="id" value="<?= htmlspecialchars($contact['id']) ?>" />
      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>" />
      <button type="submit" class="btn btn-danger">Delete</button>
    </form>
  </div>
</div>
</body>
</html>
