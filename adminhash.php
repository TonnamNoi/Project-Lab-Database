<!-- PHP Script to Hash Password and Update in Database
a simple script to hash the password and update the admin table -->

<!-- NOTE: run this script only ONCE on local to update the database -->

<?php
// Database connection
$mysqli = new mysqli('localhost', 'root', 'root', 'project1');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Define the username and password
$admin_username = 'admin';
$admin_password = '12345'; // if want to change pass word change here, and run the script once

// Hash the password using PASSWORD_DEFAULT (bcrypt)
// use password_hash() in PHP to hash the password and store it in the database
// this ensures you are using the same algorithm when you verify the password with password_verify()
$admin_password_hash = password_hash($admin_password, PASSWORD_DEFAULT);

// Update the password in the database
$sql = "UPDATE admin SET admin_password = ? WHERE admin_username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ss', $admin_password_hash, $admin_username);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Password updated successfully!";
} else {
    echo "Error updating password.";
}

$stmt->close();
$mysqli->close();
?>
