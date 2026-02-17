<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("./backend/db.php");

session_start();

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $mobile   = $_POST['mobile'];
    $gender   = $_POST['gender'];
    $address  = $_POST['address'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo "<script>alert('⚠️ Email already registered! Please login.'); window.location.href='login.php';</script>";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, mobile, gender, address, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $mobile, $gender, $address, $email, $password);

        if ($stmt->execute()) {
            // ✅ Auto-login new user
            $_SESSION['username'] = $username;
            $_SESSION['email']    = $email;
            $_SESSION['role']     = "user"; // default role

            // Redirect to index
            echo "<script>alert('✅ Registration Successful! Welcome to FitSOUL!'); window.location.href='index.php';</script>";
        } else {
            echo "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $checkEmail->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FitSOUL Register</title>
  <link rel="stylesheet" href="css/register.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

  <div class="container">
    
    <div class="left-panel">
      <h1>Welcome to <span>FitSOUL</span></h1>
      <p>Your fitness journey starts here 💪</p>
    </div>

    
    <div class="form-panel">
      <h2>Create Account</h2>
      <form method="POST">
        <input type="text" name="username" placeholder="Full Name" required />
        <input type="text" name="mobile" placeholder="Mobile Number" required />

        <select name="gender" required>
          <option value="">Select Gender</option>
          <option>Male</option>
          <option>Female</option>
          <option>Other</option>
        </select>

        <input type="text" name="address" placeholder="Address" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />

        <button type="submit" name="submit">Register</button>
      </form>
      <p class="login-link">
        Already a member? <a href="./login.php">Login here</a>
      </p>
    </div>
  </div>
</body>
</html>
