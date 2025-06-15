<?php
// logout.php
require 'inc/session.php';
// Simply destroy the session and go back to login
session_destroy();
header("Location: login.php");
exit;
?>
