<?php
session_start(); // Start the session

// Destroy the session to log out the user
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect the user back to the login page or home page
header('Location: admin-signin.php');
exit();