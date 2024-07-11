<?php
// Include config file
require_once('config.php');

// Function to fetch insights
function fetchInsights($conn) {
    $insights = [];

    // Fetch latest, max, min, avg temperature
    $sql_temp = "SELECT temperature FROM finalproject ORDER BY timestamp DESC LIMIT 1";
    $result_temp = $conn->query($sql_temp);

    if ($result_temp->num_rows > 0) {
        $row_temp = $result_temp->fetch_assoc();
        $insights['latest_temperature'] = $row_temp['temperature'];
    }

    $sql_temp_stats = "SELECT MAX(temperature) AS max_temp, MIN(temperature) AS min_temp, AVG(temperature) AS avg_temp FROM finalproject";
    $result_temp_stats = $conn->query($sql_temp_stats);

    if ($result_temp_stats->num_rows > 0) {
        $row_temp_stats = $result_temp_stats->fetch_assoc();
        $insights['max_temperature'] = $row_temp_stats['max_temp'];
        $insights['min_temperature'] = $row_temp_stats['min_temp'];
        $insights['avg_temperature'] = round($row_temp_stats['avg_temp'], 2);
    }

    // Fetch latest, max, min, avg humidity
    $sql_humidity = "SELECT humidity FROM finalproject ORDER BY timestamp DESC LIMIT 1";
    $result_humidity = $conn->query($sql_humidity);

    if ($result_humidity->num_rows > 0) {
        $row_humidity = $result_humidity->fetch_assoc();
        $insights['latest_humidity'] = $row_humidity['humidity'];
    }

    $sql_humidity_stats = "SELECT MAX(humidity) AS max_humidity, MIN(humidity) AS min_humidity, AVG(humidity) AS avg_humidity FROM finalproject";
    $result_humidity_stats = $conn->query($sql_humidity_stats);

    if ($result_humidity_stats->num_rows > 0) {
        $row_humidity_stats = $result_humidity_stats->fetch_assoc();
        $insights['max_humidity'] = $row_humidity_stats['max_humidity'];
        $insights['min_humidity'] = $row_humidity_stats['min_humidity'];
        $insights['avg_humidity'] = round($row_humidity_stats['avg_humidity'], 2);
    }

    // Fetch latest, max, min, avg light value
    $sql_light = "SELECT light FROM finalproject ORDER BY timestamp DESC LIMIT 1";
    $result_light = $conn->query($sql_light);

    if ($result_light->num_rows > 0) {
        $row_light = $result_light->fetch_assoc();
        $insights['latest_light'] = $row_light['light'];
    }

    $sql_light_stats = "SELECT MAX(light) AS max_light, MIN(light) AS min_light, AVG(light) AS avg_light FROM finalproject";
    $result_light_stats = $conn->query($sql_light_stats);

    if ($result_light_stats->num_rows > 0) {
        $row_light_stats = $result_light_stats->fetch_assoc();
        $insights['max_light'] = $row_light_stats['max_light'];
        $insights['min_light'] = $row_light_stats['min_light'];
        $insights['avg_light'] = round($row_light_stats['avg_light'], 2);
    }

    // Total object detected in 10 latest data
    $sql_object_detected = "SELECT COUNT(*) AS total_objects FROM finalproject WHERE object_detected = 1 ORDER BY timestamp DESC LIMIT 10";
    $result_object_detected = $conn->query($sql_object_detected);

    if ($result_object_detected->num_rows > 0) {
        $row_object_detected = $result_object_detected->fetch_assoc();
        $insights['total_objects_detected'] = $row_object_detected['total_objects'];
    }

    // Total and average each color
    $sql_color_totals = "SELECT color, COUNT(*) AS total_count, AVG(light) AS avg_light_per_color FROM finalproject GROUP BY color";
    $result_color_totals = $conn->query($sql_color_totals);

    $color_insights = [];
    if ($result_color_totals->num_rows > 0) {
        while ($row_color = $result_color_totals->fetch_assoc()) {
            $color_insights[] = [
                'color' => $row_color['color'],
                'total_count' => $row_color['total_count'],
                'avg_light_per_color' => round($row_color['avg_light_per_color'], 2)
            ];
        }
    }

    $insights['color_insights'] = $color_insights;

    return $insights;
}

// Fetch insights
$insights = fetchInsights($conn);

// Close database connection
$conn->close();

// Output insights as JSON
header('Content-Type: application/json');
echo json_encode($insights);
?>
