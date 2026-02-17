<?php 
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include("./backend/db.php");

// Fetch users count
$usersQuery = $conn->query("SELECT id, username, email, mobile, gender, address FROM users ORDER BY id ASC");

// Fetch plans count
$plansQuery = $conn->query("SELECT * FROM plans ORDER BY id ASC");
$totalPlans = $plansQuery->num_rows;
$totalUsers = $usersQuery->num_rows;

// Fetch purchased plans count
$purchasedQuery = $conn->query("SELECT COUNT(*) as total FROM user_plans");
$purchasedRow = $purchasedQuery->fetch_assoc();
$totalPurchased = $purchasedRow['total'] ?? 0;

// Ensure CSRF token for admin actions
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Contacts data
$contactsQueryAll = $conn->query("SELECT id, name, email, subject, message, created_at FROM contact_us ORDER BY id ASC");
$totalContacts = $contactsQueryAll ? $contactsQueryAll->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel - FitSOUL</title>
<link rel="stylesheet" href="css/admin.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Admin Navbar -->
<?php include("admin_navbar.php"); ?>

<div class="admin-container">

    <!-- Dashboard Overview -->
    <section id="dashboard" class="admin-section">
        <h2>Dashboard Overview</h2>
        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="card">
                <h3>Total Plans</h3>
                <p><?php echo $totalPlans; ?></p>
            </div>
            <div class="card">
                <h3>Total Contacts</h3>
                <p><?php echo $totalContacts; ?></p>
            </div>
            <div class="card">
                <h3>Plans Purchased</h3>
                <p><?php echo $totalPurchased; ?></p>
            </div>
        </div>
    </section>

    <!-- Manage Plans (Preview) -->
    <section id="plans" class="admin-section">
        <h2>Manage Membership Plans</h2>
        <a href="plans.php" class="btn">+ Add / Edit Plans</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Validity</th>
                    <th>Price (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Only show 5 recent plans in admin.php
                $recentPlans = $conn->query("SELECT * FROM plans ORDER BY id ASC LIMIT 5");
                while($plan = $recentPlans->fetch_assoc()): ?>
                <tr>
                    <td><?= $plan['id'] ?></td>
                    <td><?= htmlspecialchars($plan['title']) ?></td>
                    <td><?= htmlspecialchars($plan['validity']) ?></td>
                    <td><?= htmlspecialchars($plan['price']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Purchased Plans -->
    <section id="purchases" class="admin-section">
        <h2>Recent Purchased Plans</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Plan</th>
                    <th>Expiry Date</th>
                    <th>Price (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $recentPurchases = $conn->query("
                    SELECT up.id, u.username, u.email, p.title, up.start_date, up.expiry_date, up.price
                    FROM user_plans up
                    JOIN users u ON up.user_id = u.id
                    JOIN plans p ON up.plan_id = p.id
                    ORDER BY up.id ASC LIMIT 10
                ");
                if ($recentPurchases && $recentPurchases->num_rows > 0):
                    while($purchase = $recentPurchases->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($purchase['id']) ?></td>
                        <td><?= htmlspecialchars($purchase['username']) ?></td>
                        <td><?= htmlspecialchars($purchase['email']) ?></td>
                        <td><?= htmlspecialchars($purchase['title']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($purchase['expiry_date']))) ?></td>
                        <td><?= htmlspecialchars($purchase['price']) ?></td>
                    </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="6" style="text-align:center;">No purchases yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Manage Users -->
    <section id="users" class="admin-section">
        <h2>Manage Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Gender</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $usersQuery->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['mobile']) ?></td>
                    <td><?= htmlspecialchars($user['gender']) ?></td>
                    <td><?= htmlspecialchars($user['address']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Manage Contacts -->
    <section id="contacts" class="admin-section">
        <h2>Manage Contacts</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Received</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($contactsQueryAll && $contactsQueryAll->num_rows > 0): ?>
                    <?php while($c = $contactsQueryAll->fetch_assoc()): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['created_at']) ?></td>
                        <td>
                            <!-- View button -->
                            <a href="view_contact.php?id=<?= htmlspecialchars($c['id']) ?>" class="btn" style="margin-right:8px;">View</a>

                            <!-- Delete form (unchanged) -->
                            <form method="post" action="backend/contact_delete.php" onsubmit="return confirm('Delete this message?');" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>" />
                                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>" />
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No contact messages yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</div>

</body>
</html>
