<?php
// sendData.php

// Include config file
require_once('config.php');

// Check if API key matches
if ($_POST['api_key'] != API_KEY) {
    die("Invalid API Key");
}

// Extract POST data
$distance = $_POST['distance_cm'];
$light = $_POST['light'];
$temperature = $_POST['temperature'];
$humidity = $_POST['humidity'];
$color = $_POST['color'];
$object_detected = $_POST['object_detected'];

// Prepare SQL query
$sql = "INSERT INTO finalproject (distance_cm, light, temperature, humidity, color, object_detected, timestamp)
        VALUES ('$distance', '$light', '$temperature', '$humidity', '$color', '$object_detected', NOW())";

// Execute SQL query
if ($conn->query($sql) === TRUE) {
    echo "Data inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close database connection
$conn->close();
?>
