<?php
$host = 'localhost';
$user = 'root';         // your MySQL username
$pass = '';             // your MySQL password (keep blank if none)
$dbname = 'portfolio_db';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
