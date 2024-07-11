#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <DHT.h>
#include <ArduinoJson.h>

// WiFi credentials
const char* ssid = "Rumah 1 Malaysia-Maxis Fibre";
const char* password = "Waz5S8mJ18";

// Server details
const char* host = "www.plamera.net.my";
const char* sendPath = "/midterm/finalproject/sendData.php";
const char* apiKey = "222"; // Replace with your API key

// Telegram details
#define CHAT_ID "679673171"
#define BOTtoken "7342208669:AAGUmI5osHTwrCIZBvYqEUiFfVhgAUmYtpU"  // your Bot Token (Get from Botfather)

// Pin assignments
const int trigPin = D1;
const int echoPin = D2;
const int redPin = D6;
const int greenPin = D7;
const int bluePin = D8;
const int ldrPin = A0; // Analog pin for LDR (Light Sensor)
const int dhtPin = D5; // Digital pin for DHT-22

// DHT-22 setup
#define DHTTYPE DHT22
DHT dht(dhtPin, DHTTYPE);

WiFiClient client;

void setup() {
  Serial.begin(9600);

  // Initialize pin modes
  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
  pinMode(redPin, OUTPUT);
  pinMode(greenPin, OUTPUT);
  pinMode(bluePin, OUTPUT);

  // Initialize DHT sensor
  dht.begin();
  
  // Connect to WiFi
  connectWiFi();
}

void loop() {
  // Read sensor data
  int distance = calculateDistance();
  int lightValue = analogRead(ldrPin); // Read LDR pin value
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();

  // Determine if object is detected (assuming distance in cm)
  bool objectDetected = (distance < 10); // Adjust condition based on your sensor setup

  // Set LED color based on light value
  if (lightValue < 300) {
    setColor(0, 255, 0); // Green
    sendTelegramMessage("Your light have been turn on with maximum capacity.");
  } else if (lightValue >= 300 && lightValue <= 700) {
    setColor(0, 0, 255); // Blue
  } else {
    setColor(255, 0, 0); // Red
    sendTelegramMessage("Your light have been turn off.");
  }

  // Print sensor data to serial monitor
  Serial.print("Distance (cm): ");
  Serial.println(distance);
  Serial.print("Light Value: ");
  Serial.println(lightValue);
  Serial.print("Temperature (°C): ");
  Serial.println(temperature);
  Serial.print("Humidity (%): ");
  Serial.println(humidity);
  Serial.print("Object Detected: ");
  Serial.println(objectDetected ? "Yes" : "No");

  // Send Telegram message if object detected
  if (objectDetected) {
    String message = "Someone has breached your house. Distance: " + String(distance) + " cm";
    sendTelegramMessage(message);
  }

  // Send sensor data to server
  sendDataToServer(distance, lightValue, temperature, humidity, getColorName(lightValue), objectDetected);

  delay(2000); // Delay between readings
}

int calculateDistance() {
  // Ultrasonic sensor function to calculate distance
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);

  long duration = pulseIn(echoPin, HIGH);
  int distance = duration * 0.034 / 2;

  return distance;
}

void connectWiFi() {
  // Function to connect to WiFi
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void sendTelegramMessage(String message) {
  // Function to send message via Telegram
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    client.setInsecure(); // Disable SSL verification

    if (client.connect("api.telegram.org", 443)) {
      String url = String("/bot") + BOTtoken + "/sendMessage?chat_id=" + CHAT_ID + "&text=" + message;
      client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                   "Host: api.telegram.org\r\n" +
                   "Connection: close\r\n\r\n");

      while (client.connected()) {
        String line = client.readStringUntil('\n');
        if (line == "\r") {
          break;
        }
      }
      while (client.available()) {
        String line = client.readStringUntil('\n');
        Serial.println(line);
      }
    } else {
      Serial.println("Connection to Telegram failed");
    }
  } else {
    Serial.println("WiFi Disconnected");
  }
}

void sendDataToServer(int distance, int lightValue, float temperature, float humidity, String color, bool objectDetected) {
  // Function to send sensor data to server via HTTP POST
  if (client.connect(host, 80)) {
    Serial.println("Connected to server");

    // Create HTTP POST request
    String postData = "api_key=" + String(apiKey) + "&distance_cm=" + String(distance) + "&light=" + String(lightValue) + "&temperature=" + String(temperature) + "&humidity=" + String(humidity) + "&color=" + color + "&object_detected=" + String(objectDetected ? 1 : 0);

    client.print("POST " + String(sendPath) + " HTTP/1.1\r\n");
    client.print("Host: " + String(host) + "\r\n");
    client.print("Content-Type: application/x-www-form-urlencoded\r\n");
    client.print("Content-Length: " + String(postData.length()) + "\r\n\r\n");
    client.print(postData);

    Serial.println("Data sent to server: " + postData);

    // Read response from server
    while (client.connected()) {
      String line = client.readStringUntil('\n');
      if (line == "\r") {
        Serial.println("Headers received");
        break;
      }
    }

    while (client.available()) {
      String line = client.readStringUntil('\n');
      Serial.println(line);
    }

    client.stop();
  } else {
    Serial.println("Connection to server failed");
  }
}

void setColor(int redValue, int greenValue, int blueValue) {
  // Function to set RGB LED color
  analogWrite(redPin, redValue);
  analogWrite(greenPin, greenValue);
  analogWrite(bluePin, blueValue);
}

String getColorName(int lightValue) {
  // Function to get color name based on light value
  if (lightValue < 300) {
    return "Green";
  } else if (lightValue >= 300 && lightValue <= 700) {
    return "Blue";
  } else {
    return "Red";
  }
}
