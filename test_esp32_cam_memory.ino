/*
 * ESP32-CAM Memory Test
 * Simple test to verify camera initialization without RFID/WiFi complexity
 */

#include "esp_camera.h"

// Camera pin definitions for AI Thinker ESP32-CAM
#define PWDN_GPIO_NUM     32
#define RESET_GPIO_NUM    -1
#define XCLK_GPIO_NUM      0
#define SIOD_GPIO_NUM     26
#define SIOC_GPIO_NUM     27
#define Y9_GPIO_NUM       35
#define Y8_GPIO_NUM       34
#define Y7_GPIO_NUM       39
#define Y6_GPIO_NUM       36
#define Y5_GPIO_NUM       21
#define Y4_GPIO_NUM       19
#define Y3_GPIO_NUM       18
#define Y2_GPIO_NUM        5
#define VSYNC_GPIO_NUM    25
#define HREF_GPIO_NUM     23
#define PCLK_GPIO_NUM     22

void setup() {
  Serial.begin(115200);
  delay(2000);
  
  Serial.println("\n=== ESP32-CAM Memory Test ===");
  
  // Check initial memory
  Serial.printf("Total heap: %d bytes\n", ESP.getHeapSize());
  Serial.printf("Free heap: %d bytes\n", ESP.getFreeHeap());
  Serial.printf("PSRAM found: %s\n", psramFound() ? "Yes" : "No");
  
  if (psramFound()) {
    Serial.printf("Total PSRAM: %d bytes\n", ESP.getPsramSize());
    Serial.printf("Free PSRAM: %d bytes\n", ESP.getFreePsram());
  }
  
  // Test camera initialization
  if (testCameraInit()) {
    Serial.println("✅ Camera test PASSED");
    testPhotoCapture();
  } else {
    Serial.println("❌ Camera test FAILED");
  }
}

void loop() {
  // Test photo capture every 10 seconds
  delay(10000);
  Serial.println("\n--- Testing photo capture ---");
  testPhotoCapture();
}

bool testCameraInit() {
  Serial.println("\n🔧 Testing camera initialization...");
  
  camera_config_t config;
  config.ledc_channel = LEDC_CHANNEL_0;
  config.ledc_timer = LEDC_TIMER_0;
  config.pin_d0 = Y2_GPIO_NUM;
  config.pin_d1 = Y3_GPIO_NUM;
  config.pin_d2 = Y4_GPIO_NUM;
  config.pin_d3 = Y5_GPIO_NUM;
  config.pin_d4 = Y6_GPIO_NUM;
  config.pin_d5 = Y7_GPIO_NUM;
  config.pin_d6 = Y8_GPIO_NUM;
  config.pin_d7 = Y9_GPIO_NUM;
  config.pin_xclk = XCLK_GPIO_NUM;
  config.pin_pclk = PCLK_GPIO_NUM;
  config.pin_vsync = VSYNC_GPIO_NUM;
  config.pin_href = HREF_GPIO_NUM;
  config.pin_sscb_sda = SIOD_GPIO_NUM;
  config.pin_sscb_scl = SIOC_GPIO_NUM;
  config.pin_pwdn = PWDN_GPIO_NUM;
  config.pin_reset = RESET_GPIO_NUM;
  config.xclk_freq_hz = 20000000;
  config.pixel_format = PIXFORMAT_JPEG;
  
  // Conservative settings
  if(psramFound() && ESP.getFreePsram() > 2000000) {
    config.frame_size = FRAMESIZE_VGA; // 640x480
    config.jpeg_quality = 15;
    config.fb_count = 2;
    config.fb_location = CAMERA_FB_IN_PSRAM;
    config.grab_mode = CAMERA_GRAB_LATEST;
    Serial.println("Using PSRAM configuration");
  } else {
    config.frame_size = FRAMESIZE_QVGA; // 320x240
    config.jpeg_quality = 20;
    config.fb_count = 1;
    config.fb_location = CAMERA_FB_IN_DRAM;
    config.grab_mode = CAMERA_GRAB_WHEN_EMPTY;
    Serial.println("Using DRAM configuration");
  }
  
  // Initialize camera
  esp_err_t err = esp_camera_init(&config);
  if (err != ESP_OK) {
    Serial.printf("❌ Camera init failed with error 0x%x\n", err);
    
    // Try minimal settings
    Serial.println("Trying minimal settings...");
    config.frame_size = FRAMESIZE_QQVGA; // 160x120
    config.jpeg_quality = 25;
    config.fb_count = 1;
    config.fb_location = CAMERA_FB_IN_DRAM;
    
    err = esp_camera_init(&config);
    if (err != ESP_OK) {
      Serial.printf("❌ Minimal camera init also failed: 0x%x\n", err);
      return false;
    }
    Serial.println("✅ Camera initialized with minimal settings");
  } else {
    Serial.println("✅ Camera initialized successfully");
  }
  
  Serial.printf("Free heap after init: %d bytes\n", ESP.getFreeHeap());
  if (psramFound()) {
    Serial.printf("Free PSRAM after init: %d bytes\n", ESP.getFreePsram());
  }
  
  return true;
}

void testPhotoCapture() {
  Serial.println("📸 Testing photo capture...");
  Serial.printf("Free heap before capture: %d bytes\n", ESP.getFreeHeap());
  
  camera_fb_t * fb = esp_camera_fb_get();
  if(!fb) {
    Serial.println("❌ Camera capture failed");
    return;
  }
  
  Serial.printf("✅ Photo captured: %d bytes, %dx%d\n", fb->len, fb->width, fb->height);
  
  // Return frame buffer
  esp_camera_fb_return(fb);
  
  Serial.printf("Free heap after capture: %d bytes\n", ESP.getFreeHeap());
  if (psramFound()) {
    Serial.printf("Free PSRAM after capture: %d bytes\n", ESP.getFreePsram());
  }
}