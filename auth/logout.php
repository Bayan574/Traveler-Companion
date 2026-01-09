<?php
/**
 * Logout Page
 * Clears session and redirects to login
 */

session_start();

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header('Location: /auth/login.php');
exit();

