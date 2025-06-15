<?php
// Database config
$host = 'localhost';
$user = 'root';
$pass = ''; // use your MySQL root password
$db   = 'parking_system1';
$port = 3307; // as per your setup

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
