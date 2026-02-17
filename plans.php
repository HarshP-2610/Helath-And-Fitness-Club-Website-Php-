<?php
session_start();

// only admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include("./backend/db.php"); // make sure this creates $conn (mysqli)

// collect errors / success messages
$errors = [];
$success = '';

// ===== Handle Add Plan (POST) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $validity    = trim($_POST['validity'] ?? '');
    $price_raw   = trim($_POST['price'] ?? '');

    // basic validation
    if ($title === '' || $description === '' || $validity === '' || $price_raw === '') {
        $errors[] = 'All fields are required.';
    } elseif (!is_numeric($price_raw)) {
        $errors[] = 'Price must be a number.';
    }

    if (empty($errors)) {
        $price = (float)$price_raw;

        // Check if plan with same title already exists (duplicate validation)
        $checkStmt = $conn->prepare("SELECT id FROM plans WHERE LOWER(title) = LOWER(?)");
        if ($checkStmt) {
            $checkStmt->bind_param("s", $title);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                $errors[] = "A membership plan with this title already exists. Please use a different title.";
            }
            $checkStmt->close();
        }

        if (empty($errors)) {
            // prepare INSERT - check prepare result
            $sql = "INSERT INTO plans (title, description, validity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                // show DB prepare error (useful for debugging)
                $errors[] = "Database error (prepare): " . $conn->error;
            } else {
                // use 'sssd' -> string, string, string, double
                if (!$stmt->bind_param("sssd", $title, $description, $validity, $price)) {
                    $errors[] = "Bind param failed: " . $stmt->error;
                } elseif (!$stmt->execute()) {
                    $errors[] = "Execute failed: " . $stmt->error;
                } else {
                    $stmt->close();
                    // redirect to admin dashboard so counts update and admin sees new plan
                    header("Location: admin.php?success=Plan+added");
                    exit;
                }
                $stmt->close();
            }
        }
    }
}

// ===== Handle Delete (GET delete=ID) =====
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if ($delete_id > 0) {
        $stmt = $conn->prepare("DELETE FROM plans WHERE id = ?");
        if (!$stmt) {
            $errors[] = "Database error (prepare delete): " . $conn->error;
        } else {
            $stmt->bind_param("i", $delete_id);
            if (!$stmt->execute()) {
                $errors[] = "Delete failed: " . $stmt->error;
            } else {
                $stmt->close();
                header("Location: admin.php?success=Plan+deleted");
                exit;
            }
            $stmt->close();
        }
    } else {
        $errors[] = "Invalid plan id to delete.";
    }
}

// ===== Fetch plans for display (non-blocking) =====
$plansQuery = $conn->query("SELECT * FROM plans ORDER BY id DESC");
if ($plansQuery === false) {
    $errors[] = "Failed to fetch plans: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Manage Plans - FitSOUL Admin</title>
<link rel="stylesheet" href="css/plans.css">
</head>
<body>

<?php include("admin_navbar.php"); ?>

<div class="plans-container">
    <h2>Manage Membership Plans</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
          <?php foreach ($errors as $err): ?>
            <p><?php echo htmlspecialchars($err); ?></p>
          <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['success'])): ?>
      <p class="success-msg"><?php echo htmlspecialchars($_GET['success']); ?></p>
    <?php endif; ?>

    <!-- Add Plan Form -->
    <div class="form-box">
        <h3>Add New Plan</h3>
        <form method="POST" novalidate>
            <input type="text" name="title" placeholder="Plan Title" required value="<?php echo isset($title)?htmlspecialchars($title):''; ?>">
            <textarea name="description" placeholder="Plan Description" required><?php echo isset($description)?htmlspecialchars($description):''; ?></textarea>
            <input type="text" name="validity" placeholder="Validity (e.g. 1 Month, 6 Months)" required value="<?php echo isset($validity)?htmlspecialchars($validity):''; ?>">
            <input type="text" name="price" placeholder="Price (e.g. 499 or 499.99)" required value="<?php echo isset($price_raw)?htmlspecialchars($price_raw):''; ?>">
            <button type="submit" name="add_plan">Add Plan</button>
        </form>
    </div>

    <!-- Plans Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Validity</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($plansQuery && $plansQuery->num_rows > 0): ?>
                <?php while($plan = $plansQuery->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $plan['id']; ?></td>
                    <td><?php echo htmlspecialchars($plan['title']); ?></td>
                    <td><?php echo htmlspecialchars($plan['description']); ?></td>
                    <td><?php echo htmlspecialchars($plan['validity']); ?></td>
                    <td><?php echo htmlspecialchars($plan['price']); ?></td>
                    <td>
                        <a class="btn-edit" href="edit_plan.php?id=<?php echo $plan['id']; ?>">Edit</a>
                        <a class="btn-delete" href="plans.php?delete=<?php echo $plan['id']; ?>" onclick="return confirm('Delete this plan?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No plans found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
