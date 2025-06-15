<?php
// includes/db_connect.php
$host = 'localhost';
$db_name = 'parking_system1';
$user = 'root';
$pass = 'Charan@2005';         // XAMPP default has no password for root
$port = 3306;

// Enable MySQLi exceptions for errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Create connection
$conn = new mysqli($host, $user, $pass, $db_name, $port);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
