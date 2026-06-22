/*
 * Pulse Attendance - ESP32-CAM QR Scanner
 * Hardware: ESP32-CAM (AI Thinker)
 */

// #include "esp_camera.h"
// #include <WiFi.h>
// #include <HTTPClient.h>
// #include "quirc.h"

// const char* WIFI_SSID     = "YOUR_WIFI_NAME";
// const char* WIFI_PASSWORD = "YOUR_WIFI_PASSWORD";
// const char* SERVER_URL    = "http://YOUR_SERVER_IP/smart_attendance_v2/api/attendance.php";

// #define PWDN_GPIO_NUM     32
// #define RESET_GPIO_NUM    -1
// #define XCLK_GPIO_NUM      0
// #define SIOD_GPIO_NUM     26
// #define SIOC_GPIO_NUM     27
// #define Y9_GPIO_NUM       35
// #define Y8_GPIO_NUM       34
// #define Y7_GPIO_NUM       39
// #define Y6_GPIO_NUM       36
// #define Y5_GPIO_NUM       21
// #define Y4_GPIO_NUM       19
// #define Y3_GPIO_NUM       18
// #define Y2_GPIO_NUM        5
// #define VSYNC_GPIO_NUM    25
// #define HREF_GPIO_NUM     23
// #define PCLK_GPIO_NUM     22
// #define FLASH_LED_PIN      4

// struct quirc *qr;
// String lastScanned = "";
// unsigned long lastScanTime = 0;

// void setup() {
//     Serial.begin(115200);
//     pinMode(FLASH_LED_PIN, OUTPUT);
//     digitalWrite(FLASH_LED_PIN, LOW);

//     camera_config_t config;
//     config.ledc_channel = LEDC_CHANNEL_0;
//     config.ledc_timer   = LEDC_TIMER_0;
//     config.pin_d0=Y2_GPIO_NUM; config.pin_d1=Y3_GPIO_NUM; config.pin_d2=Y4_GPIO_NUM; config.pin_d3=Y5_GPIO_NUM;
//     config.pin_d4=Y6_GPIO_NUM; config.pin_d5=Y7_GPIO_NUM; config.pin_d6=Y8_GPIO_NUM; config.pin_d7=Y9_GPIO_NUM;
//     config.pin_xclk=XCLK_GPIO_NUM; config.pin_pclk=PCLK_GPIO_NUM; config.pin_vsync=VSYNC_GPIO_NUM;
//     config.pin_href=HREF_GPIO_NUM; config.pin_sscb_sda=SIOD_GPIO_NUM; config.pin_sscb_scl=SIOC_GPIO_NUM;
//     config.pin_pwdn=PWDN_GPIO_NUM; config.pin_reset=RESET_GPIO_NUM;
//     config.xclk_freq_hz = 20000000;
//     config.pixel_format = PIXFORMAT_GRAYSCALE;
//     config.frame_size   = FRAMESIZE_QVGA;
//     config.jpeg_quality = 12;
//     config.fb_count     = 1;

//     if (esp_camera_init(&config) != ESP_OK) { Serial.println("Camera init failed!"); return; }

//     WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
//     Serial.print("Connecting to WiFi");
//     while (WiFi.status() != WL_CONNECTED) { delay(500); Serial.print("."); }
//     Serial.println("\nWiFi connected: " + WiFi.localIP().toString());

//     qr = quirc_new();
//     Serial.println("Ready to scan QR codes!");
// }

// void loop() {
//     camera_fb_t *fb = esp_camera_fb_get();
//     if (!fb) { delay(100); return; }

//     if (quirc_resize(qr, fb->width, fb->height) < 0) { esp_camera_fb_return(fb); return; }

//     uint8_t *image; int w, h;
//     image = quirc_begin(qr, &w, &h);
//     memcpy(image, fb->buf, fb->len);
//     quirc_end(qr);
//     esp_camera_fb_return(fb);

//     if (quirc_count(qr) > 0) {
//         struct quirc_code code; struct quirc_data data;
//         quirc_extract(qr, 0, &code);
//         quirc_decode(&code, &data);
//         String scanned = String((char*)data.payload);
//         scanned.trim();

//         if (scanned.length() > 0 && (scanned != lastScanned || millis() - lastScanTime > 3000)) {
//             lastScanned = scanned; lastScanTime = millis();
//             Serial.println("QR Scanned: " + scanned);
//             digitalWrite(FLASH_LED_PIN, HIGH); delay(200); digitalWrite(FLASH_LED_PIN, LOW);
//             sendAttendance(scanned);
//         }
//     }
//     delay(50);
// }

// void sendAttendance(String studentCode) {
//     if (WiFi.status() != WL_CONNECTED) { Serial.println("WiFi not connected!"); return; }
//     HTTPClient http;
//     String url = String(SERVER_URL) + "?student_code=" + studentCode;
//     Serial.println("Sending to: " + url);
//     http.begin(url);
//     int httpCode = http.GET();
//     if (httpCode == 200) Serial.println("Server response: " + http.getString());
//     else Serial.println("HTTP error: " + String(httpCode));
//     http.end();
// }
