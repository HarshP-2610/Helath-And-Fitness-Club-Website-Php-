<?php
$adminPassword = "admin123"; // <-- your chosen admin password
$hash = password_hash($adminPassword, PASSWORD_BCRYPT);
echo "Hashed Password: " . $hash;
