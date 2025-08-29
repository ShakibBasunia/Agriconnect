<?php
$host = 'localhost';  // Your host
$user = 'root';       // MySQL username
$password = '';       // MySQL password
$dbname = 'agriconnect'; // Use agriconnect as database

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
