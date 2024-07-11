<?php
// config.php

// Database credentials
$servername = "localhost";
$username = "plameradbdev1";
$password = "Fadzlinaim123@";
$dbname = "plameradbdev1";

// API key
define('API_KEY', '222');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
