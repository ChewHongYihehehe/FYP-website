<?php
session_start(); // Start the session

// Unset all of the session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the home page or login page
header("Location: admin_login.php"); // Change this to your desired redirect page
exit();
