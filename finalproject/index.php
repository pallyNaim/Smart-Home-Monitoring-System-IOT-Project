<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Sensor Data</title>
    <style>
        /* Reset and basic styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa; /* Light grey background */
            padding: 20px;
        }

        .container {
            max-width: 1200px; /* Adjusted max-width for a more centered layout */
            margin: 0 auto;
            background-color: #fff; /* White background */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #6c757d; /* Dark grey heading color */
            text-align: center;
            margin-bottom: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .data-table th, .data-table td {
            border: 1px solid #dee2e6; /* Lighter grey border */
            padding: 12px;
            text-align: center;
        }

        .data-table th {
            background-color: #f0f0f0; /* Light grey background for table headers */
        }

        .graphs {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            margin-top: 30px;
        }

        .graph-container {
            flex: 1 1 400px; /* Flexbox to adjust graph container size */
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .live-update {
            margin-top: 30px;
            padding: 20px;
            background-color: #f0f0f0; /* Light grey background for live update section */
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .insights {
            margin-top: 30px;
            padding: 20px;
            background-color: #f0f0f0; /* Light grey background for insights section */
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .insights ul {
            list-style-type: none;
            padding: 0;
        }

        .insights ul li {
            margin-bottom: 10px;
        }

        .insight-container {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .temperature-insight { background-color: #ffcccc; }
        .humidity-insight { background-color: #cce6ff; }
        .light-insight { background-color: #ccffcc; }
        .object-insight { background-color: #ffffcc; }
        .color-insight { background-color: #e5ccff; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Home Sensor Data</h1>

        <!-- Table for Latest Data -->
        <h2>Latest Data</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Entry Detected</th>
                    <th>Light Intensity Value</th>
                    <th>Temperature (°C)</th>
                    <th>Humidity (%)</th>
                    <th>LED Color</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody id="latest-data">
                <!-- PHP to fetch latest entries from MySQL -->
                <?php
                // Include config file
                require_once('config.php');

                // Function to fetch latest entries
                function fetchLatestEntries($conn) {
                    $sql = "SELECT * FROM finalproject ORDER BY timestamp DESC LIMIT 10";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Format 'Entry Detected'
                            $entryDetected = ($row['object_detected'] == 1) ? 'Yes' : 'No';
                            echo "<tr>";
                            echo "<td>{$row['id']}</td>"; // Display ID
                            echo "<td>{$entryDetected}</td>";
                            echo "<td>{$row['light']}</td>";
                            echo "<td>{$row['temperature']}</td>";
                            echo "<td>{$row['humidity']}</td>";
                            echo "<td>{$row['color']}</td>";
                            echo "<td>{$row['timestamp']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No data available</td></tr>";
                    }
                }

                fetchLatestEntries($conn);
                $conn->close();
                ?>
            </tbody>
        </table>

        <!-- Line Graphs for Sensor Values -->
        <div class="graphs">
            <div class="graph-container">
                <h2>Light Intensity Value</h2>
                <canvas id="lightChart" width="300" height="150"></canvas>
            </div>
            <div class="graph-container">
                <h2>Temperature (°C)</h2>
                <canvas id="tempChart" width="300" height="150"></canvas>
            </div>
            <div class="graph-container">
                <h2>Humidity (%)</h2>
                <canvas id="humidityChart" width="300" height="150"></canvas>
            </div>
            <div class="graph-container">
                <h2>Temperature and Humidity</h2>
                <canvas id="tempHumidityChart" width="300" height="150"></canvas>
            </div>
        </div>

        <!-- Insights Section -->
        <div class="insights">
            <h2>Insights</h2>
            <div id="insights-data"></div>
        </div>
    </div>

    <script>
    // Function to fetch insights data
    function fetchInsightsData() {
        fetch('generate_insights.php')
            .then(response => response.json())
            .then(data => {
                // Display insights
                displayInsights(data);
            })
            .catch(error => {
                console.error('Error fetching insights:', error);
            });
    }

    // Function to display insights
    function displayInsights(data) {
        const insightsDiv = document.getElementById('insights-data');
        insightsDiv.innerHTML = `
            <div class="temperature-insight insight-container">
                <p><strong>Temperature Insights:</strong></p>
                <ul>
                    <li>Latest Temperature: ${data.latest_temperature} °C</li>
                    <li>Maximum Temperature: ${data.max_temperature} °C</li>
                    <li>Minimum Temperature: ${data.min_temperature} °C</li>
                    <li>Average Temperature: ${data.avg_temperature} °C</li>
                </ul>
            </div>
            <div class="humidity-insight insight-container">
                <p><strong>Humidity Insights:</strong></p>
                <ul>
                    <li>Latest Humidity: ${data.latest_humidity} %</li>
                    <li>Maximum Humidity: ${data.max_humidity} %</li>
                    <li>Minimum Humidity: ${data.min_humidity} %</li>
                    <li>Average Humidity: ${data.avg_humidity} %</li>
                </ul>
            </div>
            <div class="light-insight insight-container">
                <p><strong>Light Intensity Insights:</strong></p>
                <ul>
                    <li>Latest Light Intensity Value: ${data.latest_light}</li>
                    <li>Maximum Light Intensity Value: ${data.max_light}</li>
                    <li>Minimum Light Intensity Value: ${data.min_light}</li>
                    <li>Average Light Intensity Value: ${data.avg_light}</li>
                </ul>
            </div>
            <div class="object-insight insight-container">
                <p><strong>Total Object Detected in Latest Data:</strong> ${data.total_objects_detected}</p>
            </div>
            <div class="color-insight insight-container">
                <p><strong>Color Insights:</strong></p>
                <ul>
                    ${data.color_insights.map(color => `
                        <li>Color: ${color.color}, Total Count: ${color.total_count}, Average Light Intensity: ${color.avg_light_per_color}</li>
                    `).join('')}
                </ul>
            </div>
        `;
    }

    // Fetch insights data on page load
    document.addEventListener('DOMContentLoaded', function() {
        fetchInsightsData();
        // Initial fetch and update on page load for charts
        fetchDataForCharts();
    });

    // Function to fetch data for charts
    function fetchDataForCharts() {
        // Fetch data from PHP script to update charts
        fetch('fetch_chart_data.php')
            .then(response => response.json())
            .then(data => {
                // Extract data for charts
                const timestamps = data.timestamps;
                const lightData = data.lightData;
                const tempData = data.tempData;
                const humidityData = data.humidityData;

                // Reverse arrays to display older data on the left and newer on the right
                timestamps.reverse();
                lightData.reverse();
                tempData.reverse();
                humidityData.reverse();

                // Update Light Value chart
                updateChart('lightChart', 'Light Value', timestamps, lightData, 'rgb(75, 192, 192)');

                // Update Temperature chart
                updateChart('tempChart', 'Temperature (°C)', timestamps, tempData, 'rgb(255, 99, 132)');

                // Update Humidity chart
                updateChart('humidityChart', 'Humidity (%)', timestamps, humidityData, 'rgb(54, 162, 235)');

                // Update Temperature and Humidity chart
                updateCombinedChart('tempHumidityChart', 'Temperature and Humidity', timestamps, tempData, humidityData);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    // Function to update a chart
    function updateChart(chartId, label, labels, data, borderColor) {
        const ctx = document.getElementById(chartId).getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    fill: false,
                    borderColor: borderColor,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    x: {
                        maxTicks: 30, // Maximum 30 ticks on x-axis
                        reverse: true // Display older data on the left and newer on the right
                    },
                    y: {
                        beginAtZero: false, // Start y-axis from non-zero value if data allows
                        suggestedMin: Math.min(...data) - 1, // Adjusted minimum based on data range
                        suggestedMax: Math.max(...data) + 1 // Adjusted maximum based on data range
                    }
                }
            }
        });
    }

    // Function to update combined temperature and humidity chart
    function updateCombinedChart(chartId, label, labels, tempData, humidityData) {
        const ctx = document.getElementById(chartId).getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Temperature (°C)',
                        data: tempData,
                        fill: false,
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1
                    },
                    {
                        label: 'Humidity (%)',
                        data: humidityData,
                        fill: false,
                        borderColor: 'rgb(54, 162, 235)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        maxTicks: 30, // Maximum 30 ticks on x-axis
                        reverse: true // Display older data on the left and newer on the right
                    },
                    y: {
                        beginAtZero: false, // Start y-axis from non-zero value if data allows
                        suggestedMin: 0, // Adjusted minimum based on data range
                        suggestedMax: 100 // Adjusted maximum based on data range
                    }
                }
            }
        });
    }

    // Function to refresh page content every 5 seconds
    setInterval(() => {
        location.reload();
    }, 5000); // 5000 milliseconds = 5 seconds
    </script>

</body>
</html>
