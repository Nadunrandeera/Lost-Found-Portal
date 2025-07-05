<?php
$host = 'localhost';
$db   = 'lost_found_portal';
$user = 'root';
$pass = ''; // Use your MySQL password if set

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
