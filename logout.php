<?php
require_once '../db.php';

// Destroy session and logout
session_destroy();

// Redirect to home page
header('Location: ../index.php');
exit();
?>
