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

// Get the encrypted password and IV from the database
$sql = "SELECT admin_password, admin_iv, admin_username FROM admin WHERE admin_username = 'admin'";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    
    // Check if both the encrypted password and IV are available
    if (isset($admin['admin_password']) && isset($admin['admin_iv'])) {
        // Decode the IV from base64
        $iv = base64_decode($admin['admin_iv']);
        
        // Decrypt the password
        $decrypted_password = openssl_decrypt($admin['admin_password'], $method, $key, 0, $iv);

        // Check if decryption was successful
        if ($decrypted_password !== false) {
            echo "Decrypted Password: " . $decrypted_password . "<br>";
            
            // Now update the database with the decrypted password (not re-encrypted)
            $update_sql = "UPDATE admin SET admin_password = ? WHERE admin_username = ?";
            $stmt_update = $mysqli->prepare($update_sql);

            if ($stmt_update) {
                // Bind parameters and execute the update statement
                $stmt_update->bind_param('ss', $decrypted_password, $admin['admin_username']);

                if ($stmt_update->execute()) {
                    echo "Decrypted password updated successfully!<br>";
                } else {
                    echo "Error updating password: " . $stmt_update->error . "<br>";
                }

                // Close the update statement
                $stmt_update->close();
            } else {
                echo "Error preparing update query: " . $mysqli->error . "<br>";
            }
        } else {
            echo "Error decrypting password!<br>";
        }
    } else {
        echo "Encrypted password or IV not found in the database!<br>";
    }
} else {
    echo "No admin found with the specified username!<br>";
}

// Close the database connection
$mysqli->close();
?>
