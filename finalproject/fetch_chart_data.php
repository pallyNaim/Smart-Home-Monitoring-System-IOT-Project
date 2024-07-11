<?php
// Include config file
require_once('config.php');

// Query to fetch latest 30 entries
$sql = "SELECT * FROM finalproject ORDER BY timestamp DESC LIMIT 10";
$result = $conn->query($sql);

$timestamps = [];
$lightData = [];
$tempData = [];
$humidityData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $timestamps[] = $row['timestamp'];
        $lightData[] = $row['light'];
        $tempData[] = $row['temperature'];
        $humidityData[] = $row['humidity'];
    }
}

// Close database connection
$conn->close();

// Reverse arrays to display older data on the left and newer on the right
$timestamps = array_reverse($timestamps);
$lightData = array_reverse($lightData);
$tempData = array_reverse($tempData);
$humidityData = array_reverse($humidityData);

// Prepare data to send as JSON
$data = [
    'timestamps' => $timestamps,
    'lightData' => $lightData,
    'tempData' => $tempData,
    'humidityData' => $humidityData
];

// Output data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
