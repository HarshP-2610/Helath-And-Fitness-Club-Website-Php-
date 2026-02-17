<?php
$host = "localhost"; // Change if needed
$user = "root";      // Default XAMPP/WAMP user
$pass = "";          // Default XAMPP/WAMP password is empty
$db   = "fitsoul";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
