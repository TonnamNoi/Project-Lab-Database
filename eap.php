<?php
// Enable error reporting to debug any issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$mysqli = new mysqli('localhost', 'root', 'root', 'project1');

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} else {
    echo "Database connected successfully!<br>";
}

// Encryption settings
$key = '12345';  // Your encryption key
$method = 'aes-256-cbc'; // Encryption method

// Generate an initialization vector (IV) for the encryption
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));

// The password to encrypt
$password = 'hello123';

// Encrypt the password
$encrypted_password = openssl_encrypt($password, $method, $key, 0, $iv);

// Ensure the IV is stored as a readable format (e.g., base64 encoding)
$iv_base64 = base64_encode($iv);

// Update the encrypted password and IV for the existing admin account
$sql = "UPDATE admin SET admin_password = ?, admin_iv = ? WHERE admin_username = ?";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Statement preparation failed: " . $mysqli->error);
}

// Bind parameters and execute the statement
$admin_username = 'admin';  // The username you want to update (admin in this case)
$stmt->bind_param('sss', $encrypted_password, $iv_base64, $admin_username);

if ($stmt->execute()) {
    echo "Password encrypted and updated successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$mysqli->close();
?>
