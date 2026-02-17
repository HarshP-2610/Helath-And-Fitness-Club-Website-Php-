<?php
session_start();
include("./backend/db.php");

if (isset($_POST['login'])) {
  if (empty($_POST['email']) || empty($_POST['password'])) {
    echo "<script>alert('⚠️ Please enter email and password');</script>";
  } else {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // 'user' or 'admin'

    $query = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $query->bind_param("ss", $email, $role);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();

      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['mobile']    = $user['mobile'];
        $_SESSION['gender']    = $user['gender'];
        $_SESSION['address']   = $user['address'];
        $_SESSION['role']      = $user['role'];

        // ✅ Redirect based on role
        echo "<script>alert('✅ Login Successful! Welcome, {$user['username']}');</script>";
        if ($user['role'] === 'admin') {
          header("Location: admin.php");
        } else {
          header("Location: index.php");
        }
        exit();
      } else {
        echo "<script>alert('❌ Invalid password');</script>";
      }
    } else {
      echo "<script>alert('❌ Invalid email or role');</script>";
    }
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="css/login.css">
</head>

<body>
<?php include("navbar.php"); ?>
  <div class="login-wrapper">
    <div class="login-form-section">
      <div class="login-form-card">
        <h2>Login</h2>
        <form id="loginForm" method="POST">
  <input type="email" id="email" name="email" placeholder="Email" required>
  <input type="password" id="password" name="password" placeholder="Password" required>

  <div class="role-toggle">
    <label class="active">
      <input type="radio" name="role" value="user" checked>
      User
    </label>
    <label>
      <input type="radio" name="role" value="admin">
      Admin
    </label>
  </div>

  <input type="submit" name="login" class="login-btn" value="Login">
</form>
        <p class="register-text">
          New here? <span class="register-link" onclick="window.location.href='./register.php'">Create an account</span>
        </p>
      </div>
    </div>
  </div>

  
</body>

</html>