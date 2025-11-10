<?php
session_start();
// Unset the login_id session variable
unset($_SESSION['login_id']);
// Destroy the session
session_destroy();
// Redirect to index.php
header('Location: index.php');
exit; // Ensure that no further code is executed after the redirection
?>


