<?php
session_start();

// Only admin can access
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include("./backend/db.php"); // $conn = new mysqli(...)

$errors = [];
$success = '';
$plan = null;

// ===== Step 1: Get Plan ID =====
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request. Plan ID is required.");
}
$plan_id = intval($_GET['id']);

// ===== Step 2: Fetch Plan =====
$stmt = $conn->prepare("SELECT * FROM plans WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $plan_id);
$stmt->execute();
$result = $stmt->get_result();
$plan = $result->fetch_assoc();
$stmt->close();

if (!$plan) {
    die("Plan not found.");
}

// ===== Step 3: Handle Update (POST) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_plan'])) {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $validity    = trim($_POST['validity'] ?? '');
    $price_raw   = trim($_POST['price'] ?? '');

    // Validation
    if ($title === '' || $description === '' || $validity === '' || $price_raw === '') {
        $errors[] = 'All fields are required.';
    } elseif (!is_numeric($price_raw)) {
        $errors[] = 'Price must be a number.';
    }

    if (empty($errors)) {
        $price = (float)$price_raw;

        // Check if another plan with same title already exists (exclude current plan)
        $checkStmt = $conn->prepare("SELECT id FROM plans WHERE LOWER(title) = LOWER(?) AND id != ?");
        if ($checkStmt) {
            $checkStmt->bind_param("si", $title, $plan_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                $errors[] = "A membership plan with this title already exists. Please use a different title.";
            }
            $checkStmt->close();
        }

        if (empty($errors)) {
            $update_sql = "UPDATE plans SET title = ?, description = ?, validity = ?, price = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            if (!$stmt) {
                $errors[] = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("sssdi", $title, $description, $validity, $price, $plan_id);
                if (!$stmt->execute()) {
                    $errors[] = "Execute failed: " . $stmt->error;
                } else {
                    $stmt->close();
                    header("Location: admin.php?success=Plan+updated");
                    exit;
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Edit Plan - FitSOUL Admin</title>
<link rel="stylesheet" href="css/edit_plans.css">
</head>
<body>

<?php include("admin_navbar.php"); ?>

<div class="plans-container">
    <h2>Edit Membership Plan</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $err): ?>
                <p><?php echo htmlspecialchars($err); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <input type="text" name="title" placeholder="Plan Title" required value="<?php echo htmlspecialchars($plan['title']); ?>">
        <textarea name="description" placeholder="Plan Description" required><?php echo htmlspecialchars($plan['description']); ?></textarea>
        <input type="text" name="validity" placeholder="Validity (e.g. 1 Month)" required value="<?php echo htmlspecialchars($plan['validity']); ?>">
        <input type="text" name="price" placeholder="Price" required value="<?php echo htmlspecialchars($plan['price']); ?>">
        <button type="submit" name="update_plan">Update Plan</button>
        <a href="admin.php" class="btn-cancel">Cancel</a>
    </form>
</div>

</body>
</html>
