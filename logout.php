<?php
session_start();
session_unset();
session_destroy();

// Clear remember me cookies
setcookie("remember_username", "", time() - 3600, "/");
setcookie("remember_token", "", time() - 3600, "/");

// Redirect to login page
header("Location: login.php");
exit;

