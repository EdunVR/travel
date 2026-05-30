#include <Wire.h>
#include <SPI.h>
#include <Adafruit_GFX.h>
#include <Adafruit_ILI9341.h>
#include <Adafruit_PN532.h>
#include <Keypad_I2C.h>
#include <Keypad.h>
#include <driver/i2s.h>
#include <math.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Preferences.h>
#include <WiFiClientSecure.h>
#include <vector>
#include <esp_task_wdt.h>  // Hardware watchdog timer

/* =========================
   PIN CONFIG
========================= */

// I2C
#define SDA_PIN 6
#define SCL_PIN 7

// TFT SPI
#define TFT_CS   10
#define TFT_DC    9
#define TFT_RST  14

// I2S Audio
#define I2S_BCLK 41
#define I2S_LRC  42
#define I2S_DOUT 40
#define SAMPLE_RATE 22050
#define I2S_PORT I2S_NUM_0
#define MAX_VOLUME 32767  // Volume maksimal (16-bit signed max)

/* =========================
   OBJECTS
========================= */

Adafruit_ILI9341 tft = Adafruit_ILI9341(TFT_CS, TFT_DC, TFT_RST);
Adafruit_PN532 nfc(-1, -1);
Preferences preferences;

// Keypad
#define KEYPAD_ADDR 0x21
const byte ROWS = 4;
const byte COLS = 4;

char keys[ROWS][COLS] = {
  {'1','2','3','A'},
  {'4','5','6','B'},
  {'7','8','9','C'},
  {'*','0','#','D'}
};

byte rowPins[ROWS] = {3,2,1,0};
byte colPins[COLS] = {7,6,5,4};

Keypad_I2C keypad(makeKeymap(keys), rowPins, colPins, ROWS, COLS, KEYPAD_ADDR);

/* =========================
   LARAVEL API
========================= */
const char* serverURL = "https://hmtourtravel.com";
String apiEndpoint = "/api/morra/api/rfid";

// Untuk mengabaikan verifikasi sertifikat SSL
class InsecureWiFiClient : public WiFiClientSecure {
public:
  InsecureWiFiClient() {
    this->setInsecure();
  }
};

/* =========================
   STRUCTURES & VARIABLES
========================= */

// Mode operasi
enum Mode {
  MODE_MAIN_MENU,
  MODE_ATTENDANCE,
  MODE_REGISTER,
  MODE_WIFI_SETUP,
  MODE_ALARM_SETUP
};

Mode currentMode = MODE_MAIN_MENU;
Mode lastMode = MODE_MAIN_MENU;

// Mode dari server
String serverMode = "attendance"; // "attendance" atau "register"
unsigned long lastModeCheck = 0;
const unsigned long MODE_CHECK_INTERVAL = 2000; // Cek mode setiap 2 detik (lebih responsif!)

// Data karyawan terakhir yang tap
String lastEmployeeName = "";
String lastTapTime = "";
unsigned long lastTapDisplay = 0;
const unsigned long TAP_DISPLAY_DURATION = 2000; // Tampilkan 2 detik

// Real-time clock untuk attendance mode
unsigned long lastClockUpdate = 0;
const unsigned long CLOCK_UPDATE_INTERVAL = 1000; // Update setiap detik
int currentHour = 7;    // Default jam 07:00:00
int currentMinute = 0;
int currentSecond = 0;
unsigned long lastTimeSync = 0;
const unsigned long TIME_SYNC_INTERVAL = 3600000; // Sync setiap 1 jam

// Animation variables
int animationFrame = 0;
unsigned long lastAnimationUpdate = 0;
const unsigned long ANIMATION_INTERVAL = 50; // 20 FPS
int slideOffset = 0;

// Modern color palette
#define COLOR_PRIMARY    0x2196F3  // Material Blue
#define COLOR_SUCCESS    0x4CAF50  // Material Green  
#define COLOR_WARNING    0xFFC107  // Material Amber
#define COLOR_ERROR      0xF44336  // Material Red
#define COLOR_DARK       0x263238  // Dark Blue Grey
#define COLOR_LIGHT      0xECEFF1  // Light Blue Grey

// Watchdog timer
unsigned long lastWatchdogFeed = 0;
const unsigned long WATCHDOG_FEED_INTERVAL = 1000; // Feed watchdog setiap 1 detik
const unsigned long WDT_TIMEOUT = 30; // Hardware watchdog timeout 30 detik

// Memory monitoring
unsigned long lastMemoryCheck = 0;
const unsigned long MEMORY_CHECK_INTERVAL = 10000; // Cek memory setiap 10 detik
size_t minFreeHeap = 0xFFFFFFFF;

// Anti-hang monitoring
unsigned long lastActivityTime = 0;
const unsigned long ACTIVITY_TIMEOUT = 3600000; // 1 jam tanpa aktivitas = restart
unsigned long lastWiFiCheck = 0;
const unsigned long WIFI_CHECK_INTERVAL = 60000; // Cek WiFi setiap 1 menit
int consecutiveErrors = 0;
const int MAX_CONSECUTIVE_ERRORS = 10;

// Variabel WiFi
String ssid = "";
String password = "";
bool enteringSSID = true; // true: input SSID, false: input Password
bool isConnecting = false;

// Variabel Alarm
int alarmMasukJam = 7;
int alarmMasukMenit = 30;
int alarmKeluarJam = 16;
int alarmKeluarMenit = 0;
int alarmIstirahatMulaiJam = 12;
int alarmIstirahatMulaiMenit = 0;
int alarmIstirahatSelesaiJam = 13;
int alarmIstirahatSelesaiMenit = 0;
int alarmSettingStep = 0; // 0: menu, 1-4: setting alarm
String alarmTempInput = "";
bool alarmInputJam = true; // true: input jam, false: input menit

// Variabel input keypad (multi-tap)
char lastKey = 0;
unsigned long lastKeyPress = 0;
int keyPressCount = 0;
String currentInput = "";
String previewChar = "";
const unsigned long KEY_TIMEOUT = 800;
bool inputMode = false;

// Fitur keyboard
bool capsLock = false;
bool numLock = false;
String indicatorLine = "";

// Map untuk multi-tap keypad
const char* keyMapLower[] = {
  ".,?!1",  // Tombol 1
  "abc2",   // Tombol 2
  "def3",   // Tombol 3
  "ghi4",   // Tombol 4
  "jkl5",   // Tombol 5
  "mno6",   // Tombol 6
  "pqrs7",  // Tombol 7
  "tuv8",   // Tombol 8
  "wxyz9",  // Tombol 9
  "",       // Tombol *
  " 0",     // Tombol 0
  ""        // Tombol #
};

const char* keyMapUpper[] = {
  ".,?!1",  // Tombol 1
  "ABC2",   // Tombol 2
  "DEF3",   // Tombol 3
  "GHI4",   // Tombol 4
  "JKL5",   // Tombol 5
  "MNO6",   // Tombol 6
  "PQRS7",  // Tombol 7
  "TUV8",   // Tombol 8
  "WXYZ9",  // Tombol 9
  "",       // Tombol *
  " 0",     // Tombol 0
  ""        // Tombol #
};

// Variabel untuk status
String statusMessage = "";
unsigned long statusMessageTime = 0;

// Variabel RFID
unsigned long lastCardRead = 0;
const unsigned long CARD_READ_COOLDOWN = 2000;
String tempUID = "";
bool cardStillPresent = false;
unsigned long cardPresentStart = 0;
const unsigned long CARD_PRESENT_TIMEOUT = 5000; // 5 detik max
int pn532ErrorCount = 0;
const int MAX_PN532_ERRORS = 3;

// Untuk offline queue
struct OfflineData {
  String uid;
  String name;
  String type; // "attendance" atau "register"
  unsigned long timestamp;
  int retryCount;
};

std::vector<OfflineData> offlineQueue;
const int MAX_OFFLINE_QUEUE = 50;
const int MAX_RETRY_COUNT = 5;

// Untuk non-blocking send
bool isSendingOffline = false;
unsigned long lastOfflineSendAttempt = 0;
const unsigned long OFFLINE_SEND_INTERVAL = 5000;
int currentSendingIndex = -1;

// Timing
unsigned long lastRFIDCheck = 0;
const unsigned long RFID_CHECK_INTERVAL = 150; // Optimal untuk RFID
unsigned long lastDisplayUpdate = 0;
const unsigned long DISPLAY_UPDATE_INTERVAL = 50; // Super cepat untuk UI responsiveness (20 FPS)
unsigned long lastBlinkTime = 0;
bool blinkState = false;
unsigned long lastKeypadCheck = 0;
const unsigned long KEYPAD_CHECK_INTERVAL = 5; // Check keypad setiap 5ms (200 Hz - SUPER RESPONSIF!)

// Flag untuk mencegah update berlebihan
bool displayNeedsUpdate = true;
unsigned long lastModeChange = 0;

// Flag untuk debug
int errorCount = 0;

/* =========================
   FUNCTION PROTOTYPES
========================= */
void setupI2S();
void playBeep(uint16_t freq, uint16_t duration_ms);
void playPreviewBeep();
void showMainMenu();
void showAttendanceMode();
void showRegisterMode();
void showWiFiSetup();
void showAlarmSetup();
void updateDisplay(bool force = false);
String getCharFromKey(char key, int count);
void handleMultiTapInput(char key);
void connectToWiFi();
void sendUIDToForm(String uid);
void clearUIDCache();
bool sendDataToServer(String uid, String name, String type, bool isOfflineRetry = false);
void saveToOfflineQueue(String uid, String name, String type);
void processOfflineQueue();
void sendNextOfflineData();
void checkModeFromServer();
void forceAttendanceModeOnServer();
void checkRFID();
void checkKeypad();
void resetKeypad();
void updateIndicatorLine();
void saveAlarmSettings();
void loadAlarmSettings();
void feedWatchdog();
void checkMemory();
void cleanupStrings();

/* =========================
   AUDIO FUNCTIONS
========================= */

void setupI2S() {
  i2s_config_t i2s_config = {
    .mode = (i2s_mode_t)(I2S_MODE_MASTER | I2S_MODE_TX),
    .sample_rate = SAMPLE_RATE,
    .bits_per_sample = I2S_BITS_PER_SAMPLE_16BIT,
    .channel_format = I2S_CHANNEL_FMT_ONLY_LEFT,
    .communication_format = I2S_COMM_FORMAT_STAND_I2S,
    .intr_alloc_flags = 0,
    .dma_buf_count = 8,
    .dma_buf_len = 64,
    .use_apll = false,
    .tx_desc_auto_clear = true
  };

  i2s_pin_config_t pin_config = {
    .bck_io_num = I2S_BCLK,
    .ws_io_num = I2S_LRC,
    .data_out_num = I2S_DOUT,
    .data_in_num = I2S_PIN_NO_CHANGE
  };

  i2s_driver_install(I2S_PORT, &i2s_config, 0, NULL);
  i2s_set_pin(I2S_PORT, &pin_config);
  i2s_zero_dma_buffer(I2S_PORT);
}

void playBeep(uint16_t freq, uint16_t duration_ms) {
  static unsigned long lastBeep = 0;
  if (millis() - lastBeep < 50) return;
  lastBeep = millis();
  
  const int bufferSize = 64;
  int16_t buffer[bufferSize];

  int totalSamples = (SAMPLE_RATE * duration_ms) / 1000;
  int generated = 0;

  // Start I2S
  i2s_start(I2S_PORT);

  while (generated < totalSamples) {
    // Feed watchdog during long beep
    if (generated % 512 == 0) {
      yield();
    }
    
    for (int i = 0; i < bufferSize && generated < totalSamples; i++) {
      float t = (generated + i) / (float)SAMPLE_RATE;
      buffer[i] = MAX_VOLUME * sin(2 * PI * freq * t);  // Volume maksimal
    }

    size_t bytes_written;
    i2s_write(I2S_PORT, buffer, bufferSize * sizeof(int16_t),
              &bytes_written, 100);

    generated += bufferSize;
    
    // Prevent infinite loop
    if (generated > totalSamples * 2) {
      Serial.println("⚠️ Beep timeout, breaking");
      break;
    }
  }

  // Stop I2S
  i2s_stop(I2S_PORT);
  i2s_zero_dma_buffer(I2S_PORT);
  
  yield();
}

void playWelcomeMelody() {
  playBeep(523, 100);  // C
  delay(50);
  playBeep(659, 100);  // E
  delay(50);
  playBeep(784, 150);  // G
}

void playSuccessMelody() {
  playBeep(659, 80);   // E
  delay(30);
  playBeep(784, 80);   // G
  delay(30);
  playBeep(1047, 120); // C high
}

void playErrorMelody() {
  playBeep(392, 100);  // G low
  delay(50);
  playBeep(330, 150);  // E low
}

void playTapSound() {
  playBeep(1200, 30);
}

void playPreviewBeep() {
  playBeep(1200, 20);
}

/* =========================
   DISPLAY FUNCTIONS
========================= */

void updateClock() {
  unsigned long now = millis();
  if (now - lastClockUpdate >= CLOCK_UPDATE_INTERVAL) {
    lastClockUpdate = now;
    currentSecond++;
    if (currentSecond >= 60) {
      currentSecond = 0;
      currentMinute++;
      if (currentMinute >= 60) {
        currentMinute = 0;
        currentHour++;
        if (currentHour >= 24) {
          currentHour = 0;
        }
      }
    }
  }
}

void syncTimeFromServer() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("⚠️ WiFi not connected, cannot sync time");
    return;
  }
  
  Serial.println("🕐 Syncing time from server...");
  
  InsecureWiFiClient client;
  client.setTimeout(3000);
  
  HTTPClient http;
  
  String url = String(serverURL) + apiEndpoint + "/time";
  
  http.begin(client, url);
  http.setTimeout(3000);
  
  int httpResponseCode = http.GET();
  
  if (httpResponseCode == 200) {
    String response = http.getString();
    
    Serial.print("📨 Time response: ");
    Serial.println(response);
    
    DynamicJsonDocument doc(256);
    DeserializationError error = deserializeJson(doc, response);
    
    if (!error && doc.containsKey("time")) {
      String timeStr = doc["time"].as<String>(); // Format: "HH:MM:SS"
      
      // Parse time
      currentHour = timeStr.substring(0, 2).toInt();
      currentMinute = timeStr.substring(3, 5).toInt();
      currentSecond = timeStr.substring(6, 8).toInt();
      
      // RESET lastClockUpdate untuk mulai hitung dari sekarang
      lastClockUpdate = millis();
      
      Serial.printf("✅ Time synced: %02d:%02d:%02d\n", 
                    currentHour, currentMinute, currentSecond);
      
      // Force update display
      displayNeedsUpdate = true;
    } else {
      Serial.println("❌ Failed to parse time from server");
      if (error) {
        Serial.print("JSON error: ");
        Serial.println(error.c_str());
      }
    }
    
    response = String();
  }
  
  http.end();
  client.stop();
  feedWatchdog();
}

void drawClock(int x, int y, int size) {
  char timeStr[9];
  snprintf(timeStr, sizeof(timeStr), "%02d:%02d:%02d", 
           currentHour, currentMinute, currentSecond);
  
  // Shadow effect
  tft.setTextColor(COLOR_DARK);
  tft.setTextSize(size);
  tft.setCursor(x + 2, y + 2);
  tft.print(timeStr);
  
  // Main text
  tft.setTextColor(ILI9341_WHITE);
  tft.setCursor(x, y);
  tft.print(timeStr);
}

void drawWiFiIndicator(int x, int y) {
  uint16_t color = (WiFi.status() == WL_CONNECTED) ? COLOR_SUCCESS : COLOR_ERROR;
  
  // WiFi bars (3 bars)
  for (int i = 0; i < 3; i++) {
    int h = 4 + (i * 3);
    tft.fillRect(x + (i * 4), y + (12 - h), 3, h, color);
  }
}

void drawCheckmark(int x, int y, int size, uint16_t color) {
  // Animated checkmark
  tft.fillCircle(x, y, size/2, color);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(size/10);
  tft.setCursor(x - 3, y - 5);
  tft.print("✓");
}

void updateIndicatorLine() {
  indicatorLine = "";
  if (capsLock) indicatorLine += "CAPS ";
  if (numLock) indicatorLine += "NUM ";
  if (indicatorLine.length() > 0) {
    indicatorLine = "[" + indicatorLine + "]";
  }
}

void showMainMenu() {
  tft.fillScreen(ILI9341_BLACK);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(2);
  tft.setCursor(20, 10);
  tft.print("ATTENDANCE SYSTEM");
  
  tft.drawFastHLine(0, 35, 320, ILI9341_WHITE);
  
  tft.setTextSize(2);
  tft.setTextColor(ILI9341_CYAN);
  tft.setCursor(20, 60);
  tft.print("1. Attendance");
  tft.setCursor(20, 90);
  tft.print("2. Register");
  tft.setCursor(20, 120);
  tft.print("3. WiFi Setup");
  tft.setCursor(20, 150);
  tft.print("4. Alarm Setup");
  
  // Tampilkan mode dari server
  tft.setTextSize(1);
  tft.setTextColor(ILI9341_YELLOW);
  tft.setCursor(20, 180);
  tft.print("Server Mode: " + serverMode);
  
  tft.setTextColor(ILI9341_GREEN);
  tft.setCursor(20, 200);
  
  if (WiFi.status() == WL_CONNECTED) {
    tft.print("WiFi: Connected");
    tft.setCursor(20, 215);
    tft.print(WiFi.localIP().toString());
  } else {
    tft.print("WiFi: Disconnected");
  }
  
  if (offlineQueue.size() > 0) {
    tft.setTextColor(ILI9341_YELLOW);
    tft.setCursor(20, 230);
    tft.print("Offline: " + String(offlineQueue.size()));
  }
}

void showAttendanceMode() {
  // Modern gradient background (dark blue to darker)
  for (int y = 0; y < 240; y++) {
    uint8_t brightness = 255 - (y / 2);
    uint16_t color = tft.color565(0, brightness/4, brightness/2);
    tft.drawFastHLine(0, y, 320, color);
  }
  
  // Header bar dengan rounded corners
  tft.fillRoundRect(10, 10, 300, 50, 10, COLOR_DARK);
  tft.drawRoundRect(10, 10, 300, 50, 10, COLOR_PRIMARY);
  
  tft.setTextColor(COLOR_PRIMARY);
  tft.setTextSize(2);
  tft.setCursor(60, 20);
  tft.print("ATTENDANCE");
  
  // Jam berjalan (besar dan jelas) - akan di-update partial di loop()
  drawClock(85, 40, 2);
  
  // WiFi indicator di pojok kanan atas
  drawWiFiIndicator(280, 25);
  
  // Status bar
  tft.fillRoundRect(10, 70, 300, 30, 8, COLOR_LIGHT);
  tft.setTextColor(COLOR_DARK);
  tft.setTextSize(1);
  tft.setCursor(20, 82);
  tft.print("Tap your RFID card to record");
  
  // Server mode badge
  uint16_t badgeColor = (serverMode == "attendance") ? COLOR_SUCCESS : COLOR_WARNING;
  tft.fillRoundRect(10, 110, 100, 25, 5, badgeColor);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(1);
  tft.setCursor(20, 118);
  tft.print("Mode: ");
  tft.print(serverMode);
  
  // INDIKATOR "ANGKAT KARTU!" - PRIORITAS TERTINGGI
  if (statusMessage == "ANGKAT KARTU!" && millis() - statusMessageTime < 5000) {
    // Warning box besar di tengah
    tft.fillRoundRect(40, 140, 240, 60, 10, COLOR_WARNING);
    tft.drawRoundRect(40, 140, 240, 60, 10, ILI9341_RED);
    tft.drawRoundRect(41, 141, 238, 58, 10, ILI9341_RED);
    
    tft.setTextColor(ILI9341_BLACK);
    tft.setTextSize(3);
    tft.setCursor(50, 155);
    tft.print("ANGKAT");
    tft.setCursor(60, 175);
    tft.print("KARTU!");
    
  } 
  // Tampilkan data tap terakhir dengan animasi
  else if (lastEmployeeName.length() > 0 && millis() - lastTapDisplay < TAP_DISPLAY_DURATION) {
    // Animated slide-in effect
    int targetY = 145;
    if (slideOffset < targetY) {
      slideOffset += 20; // Slide speed lebih cepat
      if (slideOffset > targetY) slideOffset = targetY;
    }
    
    // CENTANG BESAR DI TENGAH (drawn checkmark)
    int checkX = 160;
    int checkY = 165;
    int checkRadius = 35;
    
    // Circle background untuk centang
    tft.fillCircle(checkX, checkY, checkRadius, COLOR_SUCCESS);
    tft.drawCircle(checkX, checkY, checkRadius, ILI9341_WHITE);
    tft.drawCircle(checkX, checkY, checkRadius - 1, ILI9341_WHITE);
    
    // Draw checkmark (✓) dengan garis
    // Bagian pendek (kiri bawah ke tengah)
    int x1 = checkX - 15;
    int y1 = checkY;
    int x2 = checkX - 5;
    int y2 = checkY + 12;
    
    // Bagian panjang (tengah ke kanan atas)
    int x3 = checkX + 18;
    int y3 = checkY - 15;
    
    // Draw thick checkmark (multiple lines untuk ketebalan)
    for (int i = -3; i <= 3; i++) {
      // Garis pendek
      tft.drawLine(x1 + i, y1, x2 + i, y2, ILI9341_WHITE);
      // Garis panjang
      tft.drawLine(x2 + i, y2, x3 + i, y3, ILI9341_WHITE);
    }
    
    // Employee info box di bawah centang
    tft.fillRoundRect(10, 210, 300, 25, 8, COLOR_DARK);
    tft.setTextColor(ILI9341_WHITE);
    tft.setTextSize(1);
    tft.setCursor(20, 218);
    tft.print(lastEmployeeName);
    tft.print(" - ");
    tft.print(lastTapTime);
    
  } else {
    slideOffset = 0; // Reset animation
    animationFrame = 0;
    
    // Idle state - Static RFID icon (TIDAK ADA ANIMASI)
    tft.drawRoundRect(135, 155, 50, 35, 5, COLOR_PRIMARY);
    tft.setTextColor(COLOR_PRIMARY);
    tft.setTextSize(1);
    tft.setCursor(150, 170);
    tft.print("RFID");
    
    // Static instruction
    tft.setTextColor(COLOR_LIGHT);
    tft.setCursor(110, 200);
    tft.print("Ready to scan");
  }
  
  // Offline queue indicator
  if (offlineQueue.size() > 0) {
    tft.fillRoundRect(220, 110, 90, 25, 5, COLOR_WARNING);
    tft.setTextColor(ILI9341_BLACK);
    tft.setTextSize(1);
    tft.setCursor(230, 118);
    tft.print("Offline: ");
    tft.print(offlineQueue.size());
  }
  
  // Footer dengan instruksi
  tft.setTextColor(COLOR_LIGHT);
  tft.setTextSize(1);
  tft.setCursor(95, 225);
  tft.print("Press # for menu");
}

void showRegisterMode() {
  tft.fillScreen(ILI9341_GREEN);
  tft.setTextColor(ILI9341_BLACK);
  tft.setTextSize(2);
  tft.setCursor(40, 20);
  tft.print("REGISTER MODE");
  
  tft.setTextSize(1);
  
  tft.setCursor(20, 50);
  tft.print("Server Mode: " + serverMode);
  
  if (tempUID.length() == 0) {
    tft.setCursor(20, 80);
    tft.print("Tap your card...");
  } else {
    tft.setCursor(20, 70);
    tft.print("Card: " + tempUID);
  }
  
  tft.setCursor(20, 100);
  tft.print("Press # to menu");
  
  if (inputMode) {
    // Kotak input
    tft.drawRect(10, 120, 300, 35, ILI9341_BLACK);
    tft.fillRect(11, 121, 298, 33, ILI9341_WHITE);
    
    tft.setTextColor(ILI9341_BLACK);
    tft.setTextSize(2);
    tft.setCursor(20, 128);
    tft.print(currentInput);
    
    // Preview karakter
    if (lastKey != 0 && previewChar.length() > 0) {
      tft.setTextColor(ILI9341_BLUE);
      tft.setCursor(20 + (currentInput.length() * 12), 128);
      tft.print(previewChar);
    }
    
    // Kursor berkedip
    if (lastKey == 0 && blinkState) {
      tft.setTextColor(ILI9341_BLACK);
      tft.setCursor(20 + (currentInput.length() * 12), 128);
      tft.print("_");
    }
    
    // Tombol fungsi
    tft.setTextSize(1);
    tft.setTextColor(ILI9341_BLACK);
    tft.setCursor(20, 170);
    tft.print("A:Caps B:Num  #:Done");
    tft.setCursor(20, 185);
    tft.print("*:Del 0:Space");
    
    // Indicator
    updateIndicatorLine();
    if (indicatorLine.length() > 0) {
      tft.setCursor(20, 210);
      tft.print(indicatorLine);
    }
  } else if (tempUID.length() > 0) {
    tft.setCursor(20, 140);
    tft.print("Press any key to start");
  }
  
  if (offlineQueue.size() > 0) {
    tft.setCursor(20, 230);
    tft.print("Offline: " + String(offlineQueue.size()));
  }
}

void showWiFiSetup() {
  tft.fillScreen(ILI9341_DARKGREY);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(2);
  tft.setCursor(30, 10);
  tft.print("WIFI SETUP");
  
  tft.setTextSize(1);
  
  // SSID
  tft.setCursor(10, 45);
  tft.print("SSID" + String(enteringSSID ? " [EDIT]" : ""));
  tft.drawRect(9, 55, 302, 25, enteringSSID ? ILI9341_YELLOW : ILI9341_WHITE);
  tft.fillRect(10, 56, 300, 23, ILI9341_BLACK);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(2);
  tft.setCursor(15, 58);
  tft.print(ssid);
  
  if (enteringSSID && inputMode) {
    if (lastKey != 0 && previewChar.length() > 0) {
      tft.setTextColor(ILI9341_YELLOW);
      tft.setCursor(15 + (ssid.length() * 12), 58);
      tft.print(previewChar);
    }
    if (lastKey == 0 && blinkState) {
      tft.setTextColor(ILI9341_WHITE);
      tft.setCursor(15 + (ssid.length() * 12), 58);
      tft.print("_");
    }
  }
  
  // PASSWORD - TANPA MASKING
  tft.setTextSize(1);
  tft.setTextColor(ILI9341_WHITE);
  tft.setCursor(10, 90);
  tft.print("PASSWORD" + String(!enteringSSID ? " [EDIT]" : ""));
  tft.drawRect(9, 100, 302, 25, !enteringSSID ? ILI9341_YELLOW : ILI9341_WHITE);
  tft.fillRect(10, 101, 300, 23, ILI9341_BLACK);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(2);
  tft.setCursor(15, 103);
  tft.print(password);
  
  if (!enteringSSID && inputMode) {
    if (lastKey != 0 && previewChar.length() > 0) {
      tft.setTextColor(ILI9341_YELLOW);
      tft.setCursor(15 + (password.length() * 12), 103);
      tft.print(previewChar);
    }
    if (lastKey == 0 && blinkState) {
      tft.setTextColor(ILI9341_WHITE);
      tft.setCursor(15 + (password.length() * 12), 103);
      tft.print("_");
    }
  }
  
  // Tombol Connect
  tft.setTextSize(2);
  tft.setTextColor(ILI9341_GREEN);
  tft.drawRect(100, 140, 120, 35, ILI9341_GREEN);
  tft.setCursor(120, 148);
  tft.print("CONNECT");
  
  // Instruksi
  tft.setTextSize(1);
  tft.setTextColor(ILI9341_YELLOW);
  tft.setCursor(10, 190);
  tft.print("A:Caps B:Num  C:Connect");
  tft.setCursor(10, 205);
  tft.print("D:Switch Field  *:Del");
  tft.setCursor(10, 220);
  tft.print("0:Space  #:Menu");
  
  // Indicator
  updateIndicatorLine();
  if (indicatorLine.length() > 0) {
    tft.setCursor(10, 235);
    tft.print(indicatorLine);
  }
  
  if (isConnecting) {
    tft.setCursor(10, 175);
    tft.setTextColor(ILI9341_GREEN);
    tft.print("Connecting...");
  }
}

void showAlarmSetup() {
  tft.fillScreen(ILI9341_ORANGE);
  tft.setTextColor(ILI9341_BLACK);
  tft.setTextSize(2);
  tft.setCursor(40, 10);
  tft.print("ALARM SETUP");
  
  tft.setTextSize(1);
  
  // Menu alarm dengan highlight jika sedang diedit
  tft.setCursor(20, 50);
  if (alarmSettingStep == 1) tft.setTextColor(ILI9341_RED);
  else tft.setTextColor(ILI9341_BLACK);
  tft.print("> ");
  tft.printf("1. Masuk: %02d:%02d", alarmMasukJam, alarmMasukMenit);
  
  tft.setCursor(20, 70);
  if (alarmSettingStep == 2) tft.setTextColor(ILI9341_RED);
  else tft.setTextColor(ILI9341_BLACK);
  tft.print("> ");
  tft.printf("2. Istirahat: %02d:%02d", alarmIstirahatMulaiJam, alarmIstirahatMulaiMenit);
  
  tft.setCursor(20, 90);
  if (alarmSettingStep == 3) tft.setTextColor(ILI9341_RED);
  else tft.setTextColor(ILI9341_BLACK);
  tft.print("> ");
  tft.printf("3. Kembali: %02d:%02d", alarmIstirahatSelesaiJam, alarmIstirahatSelesaiMenit);
  
  tft.setCursor(20, 110);
  if (alarmSettingStep == 4) tft.setTextColor(ILI9341_RED);
  else tft.setTextColor(ILI9341_BLACK);
  tft.print("> ");
  tft.printf("4. Pulang: %02d:%02d", alarmKeluarJam, alarmKeluarMenit);
  
  // Preview suara
  tft.setTextColor(ILI9341_BLACK);
  tft.drawRect(100, 140, 120, 30, ILI9341_BLACK);
  tft.setCursor(110, 148);
  tft.print("PREVIEW");
  
  // Instruksi
  tft.setCursor(20, 180);
  tft.print("1-4:Pilih  C:Preview");
  tft.setCursor(20, 195);
  tft.print("D:Simpan  #:Menu");
  
  // Input jam jika sedang setting
  if (alarmSettingStep > 0) {
    tft.drawRect(50, 220, 220, 30, ILI9341_BLACK);
    tft.fillRect(51, 221, 218, 28, ILI9341_WHITE);
    tft.setTextColor(ILI9341_BLACK);
    tft.setTextSize(2);
    
    if (alarmTempInput.length() == 0) {
      tft.setCursor(60, 226);
      tft.print("__:__");
    } else {
      tft.setCursor(60, 226);
      String displayTime = alarmTempInput;
      if (displayTime.length() < 2) displayTime = String(displayTime) + "_";
      else if (displayTime.length() == 2) displayTime = displayTime + ":__";
      else if (displayTime.length() == 3) displayTime = displayTime.substring(0,2) + ":" + displayTime.substring(2) + "_";
      else if (displayTime.length() >= 4) displayTime = displayTime.substring(0,2) + ":" + displayTime.substring(2,4);
      tft.print(displayTime);
    }
    
    if (blinkState) {
      int cursorPos = 60;
      if (alarmTempInput.length() < 2) cursorPos += (alarmTempInput.length() * 12);
      else if (alarmTempInput.length() < 4) cursorPos += (alarmTempInput.length() * 12) + 12;
      else cursorPos = 60 + (5 * 12);
      
      tft.fillRect(cursorPos, 226, 12, 20, ILI9341_BLACK);
    }
    
    tft.setTextSize(1);
    tft.setCursor(130, 255);
    tft.print("Input jam (JJMM)");
  }
}

void updateDisplay(bool force) {
  // Cek apakah mode berubah
  if (currentMode != lastMode) {
    lastMode = currentMode;
    displayNeedsUpdate = true;
    lastModeChange = millis();
  }
  
  // Update jika diperlukan
  if (displayNeedsUpdate || force) {
    switch(currentMode) {
      case MODE_MAIN_MENU: showMainMenu(); break;
      case MODE_ATTENDANCE: showAttendanceMode(); break;
      case MODE_REGISTER: showRegisterMode(); break;
      case MODE_WIFI_SETUP: showWiFiSetup(); break;
      case MODE_ALARM_SETUP: showAlarmSetup(); break;
    }
    displayNeedsUpdate = false;
  } else {
    // Update hanya bagian yang berubah (kursor)
    if (millis() - lastBlinkTime > 500) {
      lastBlinkTime = millis();
      blinkState = !blinkState;
      
      if (currentMode == MODE_REGISTER && inputMode && lastKey == 0) {
        int cursorX = 20 + (currentInput.length() * 12);
        tft.fillRect(cursorX, 128, 12, 16, ILI9341_WHITE);
        if (blinkState) {
          tft.setTextColor(ILI9341_BLACK);
          tft.setCursor(cursorX, 128);
          tft.print("_");
        }
      }
      else if (currentMode == MODE_WIFI_SETUP && inputMode && lastKey == 0) {
        if (enteringSSID) {
          int cursorX = 15 + (ssid.length() * 12);
          tft.fillRect(cursorX, 58, 12, 20, ILI9341_BLACK);
          if (blinkState) {
            tft.setTextColor(ILI9341_WHITE);
            tft.setCursor(cursorX, 58);
            tft.print("_");
          }
        } else {
          int cursorX = 15 + (password.length() * 12);
          tft.fillRect(cursorX, 103, 12, 20, ILI9341_BLACK);
          if (blinkState) {
            tft.setTextColor(ILI9341_WHITE);
            tft.setCursor(cursorX, 103);
            tft.print("_");
          }
        }
      }
      else if (currentMode == MODE_ALARM_SETUP && alarmSettingStep > 0) {
        displayNeedsUpdate = true;
      }
    }
  }
}

/* =========================
   KEYPAD FUNCTIONS
========================= */

String getCharFromKey(char key, int count) {
  if (key >= '1' && key <= '9') {
    int index = key - '1';
    const char* chars;
    
    if (numLock) {
      return String(key);
    } else if (capsLock) {
      chars = keyMapUpper[index];
    } else {
      chars = keyMapLower[index];
    }
    
    int len = strlen(chars);
    return String(chars[(count - 1) % len]);
  }
  if (key == '0') return " ";
  return "";
}

void handleMultiTapInput(char key) {
  unsigned long now = millis();
  
  // Hitung preview character
  if (key >= '1' && key <= '9' && !numLock) {
    int index = key - '1';
    const char* chars = capsLock ? keyMapUpper[index] : keyMapLower[index];
    int len = strlen(chars);
    previewChar = String(chars[(keyPressCount) % len]);
  } else if (key == '0') {
    previewChar = " ";
  } else if (numLock && key >= '1' && key <= '9') {
    previewChar = String(key);
  }
  
  // Jika tombol berbeda atau timeout
  if (key != lastKey || (now - lastKeyPress > KEY_TIMEOUT)) {
    if (lastKey != 0 && keyPressCount > 0) {
      String char_to_add = getCharFromKey(lastKey, keyPressCount);
      currentInput += char_to_add;
      previewChar = "";
      playPreviewBeep();
    }
    lastKey = key;
    keyPressCount = 1;
  } else {
    keyPressCount++;
    playPreviewBeep();
  }
  
  lastKeyPress = now;
  
  // Update variabel
  if (currentMode == MODE_WIFI_SETUP) {
    if (enteringSSID) {
      ssid = currentInput;
    } else {
      password = currentInput;
    }
  }
  
  displayNeedsUpdate = true;
}

void checkKeypad() {
  char key = keypad.getKey(); // Langsung ambil key tanpa loop delay
  
  if (key == 0) {
    // Cek timeout untuk preview
    if (lastKey != 0 && millis() - lastKeyPress > KEY_TIMEOUT) {
      if (keyPressCount > 0) {
        String char_to_add = getCharFromKey(lastKey, keyPressCount);
        currentInput += char_to_add;
        lastKey = 0;
        keyPressCount = 0;
        previewChar = "";
        
        if (currentMode == MODE_WIFI_SETUP) {
          if (enteringSSID) ssid = currentInput;
          else password = currentInput;
        }
        
        displayNeedsUpdate = true;
      }
    }
    return;
  }
  
  errorCount = 0;
  
  // Minimal logging untuk speed (hanya di debug mode)
  // Serial.print("Key: ");
  // Serial.println(key);
  
  playBeep(1000, 30);
  
  // Tombol fungsi A, B, C, D
  if (key == 'A') {
    capsLock = !capsLock;
    previewChar = "";
    displayNeedsUpdate = true;
    return;
  }
  else if (key == 'B') {
    numLock = !numLock;
    previewChar = "";
    displayNeedsUpdate = true;
    return;
  }
  else if (key == 'C') {
    if (currentMode == MODE_WIFI_SETUP) {
      connectToWiFi();
    }
    else if (currentMode == MODE_ALARM_SETUP) {
      // Preview suara alarm
      playBeep(1000, 200);
      delay(100);
      playBeep(1200, 200);
      delay(100);
      playBeep(1400, 400);
      statusMessage = "Preview Alarm";
      statusMessageTime = millis();
    }
    return;
  }
  else if (key == 'D') {
    if (currentMode == MODE_WIFI_SETUP) {
      // Pindah fokus antara SSID dan Password
      enteringSSID = !enteringSSID;
      if (enteringSSID) {
        currentInput = ssid;
      } else {
        currentInput = password;
      }
      inputMode = true;
      lastKey = 0;
      displayNeedsUpdate = true;
      playBeep(1200, 50);
    }
    else if (currentMode == MODE_ALARM_SETUP) {
      saveAlarmSettings();
      statusMessage = "Alarm Saved!";
      statusMessageTime = millis();
      playBeep(2000, 200);
      alarmSettingStep = 0;
      inputMode = false;
      displayNeedsUpdate = true;
    }
    return;
  }
  
  // Tombol # untuk kembali ke menu
  if (key == '#') {
    if (currentMode == MODE_REGISTER && inputMode && currentInput.length() > 0) {
      statusMessage = "Registering...";
      displayNeedsUpdate = true;
      
      if (sendDataToServer(tempUID, currentInput, "register", false)) {
        statusMessage = "Registered!";
        playBeep(2000, 200);
      } else {
        saveToOfflineQueue(tempUID, currentInput, "register");
        statusMessage = "Offline saved";
        playBeep(1000, 100);
      }
    }
    
    currentMode = MODE_MAIN_MENU;
    inputMode = false;
    tempUID = "";
    enteringSSID = true;
    alarmSettingStep = 0;
    lastKey = 0;
    currentInput = "";
    previewChar = "";
    capsLock = false;
    numLock = false;
    statusMessageTime = millis();
    displayNeedsUpdate = true;
    return;
  }
  
  // ===== HANDLE BERDASARKAN MODE =====
  
  // Mode ALARM SETUP
  if (currentMode == MODE_ALARM_SETUP) {
    if (alarmSettingStep == 0) {
      // Mode menu alarm
      if (key >= '1' && key <= '4') {
        alarmSettingStep = key - '0';
        alarmTempInput = "";
        inputMode = true;
        displayNeedsUpdate = true;
      }
    } else {
      // Mode input jam
      if (key >= '0' && key <= '9') {
        if (alarmTempInput.length() < 4) {
          alarmTempInput += key;
          displayNeedsUpdate = true;
          
          // Auto advance setelah 4 digit
          if (alarmTempInput.length() == 4) {
            // Parse dan simpan
            int jam = alarmTempInput.substring(0, 2).toInt();
            int menit = alarmTempInput.substring(2, 4).toInt();
            
            if (jam >= 0 && jam < 24 && menit >= 0 && menit < 60) {
              switch(alarmSettingStep) {
                case 1: alarmMasukJam = jam; alarmMasukMenit = menit; break;
                case 2: alarmIstirahatMulaiJam = jam; alarmIstirahatMulaiMenit = menit; break;
                case 3: alarmIstirahatSelesaiJam = jam; alarmIstirahatSelesaiMenit = menit; break;
                case 4: alarmKeluarJam = jam; alarmKeluarMenit = menit; break;
              }
              playBeep(2000, 100);
              alarmSettingStep = 0;
              inputMode = false;
            } else {
              // Input tidak valid
              playBeep(500, 200);
              alarmTempInput = "";
            }
            displayNeedsUpdate = true;
          }
        }
      }
      else if (key == '*') {
        if (alarmTempInput.length() > 0) {
          alarmTempInput.remove(alarmTempInput.length() - 1);
          displayNeedsUpdate = true;
        }
      }
    }
    return;
  }
  
  // Mode WIFI SETUP
  if (currentMode == MODE_WIFI_SETUP) {
    if (inputMode) {
      if (key == '*') {
        if (currentInput.length() > 0) {
          currentInput.remove(currentInput.length() - 1);
        }
        lastKey = 0;
        previewChar = "";
        
        if (enteringSSID) {
          ssid = currentInput;
        } else {
          password = currentInput;
        }
        displayNeedsUpdate = true;
      }
      else if (key >= '0' && key <= '9') {
        handleMultiTapInput(key);
      }
    }
    return;
  }
  
  // Mode REGISTER
  if (currentMode == MODE_REGISTER) {
    if (tempUID.length() > 0 && !inputMode) {
      inputMode = true;
      currentInput = "";
      handleMultiTapInput(key);
    }
    else if (inputMode) {
      if (key == '*') {
        if (currentInput.length() > 0) {
          currentInput.remove(currentInput.length() - 1);
        }
        lastKey = 0;
        previewChar = "";
        displayNeedsUpdate = true;
      }
      else if (key >= '0' && key <= '9') {
        handleMultiTapInput(key);
      }
    }
    return;
  }
  
  // Mode MAIN MENU
  if (currentMode == MODE_MAIN_MENU) {
    if (key == '1') {
      currentMode = MODE_ATTENDANCE;
      serverMode = "attendance";
      tempUID = "";
      inputMode = false;
      currentInput = "";
      displayNeedsUpdate = true;
      
      // Kirim ke server untuk reset mode ke attendance
      forceAttendanceModeOnServer();
      
      Serial.println("📌 Navigasi ke ATTENDANCE - server notified");
    }
    else if (key == '2') {
      currentMode = MODE_REGISTER;
      displayNeedsUpdate = true;
    }
    else if (key == '3') {
      currentMode = MODE_WIFI_SETUP;
      enteringSSID = true;
      ssid = preferences.getString("ssid", "");
      password = preferences.getString("password", "");
      currentInput = ssid;
      inputMode = true;
      displayNeedsUpdate = true;
    }
    else if (key == '4') {
      currentMode = MODE_ALARM_SETUP;
      loadAlarmSettings();
      alarmSettingStep = 0;
      displayNeedsUpdate = true;
    }
  }
}

void resetKeypad() {
  Serial.println("Resetting keypad...");
  keypad.begin();
  keypad.setHoldTime(200);
  keypad.setDebounceTime(20);
  delay(100);
}

void resetPN532() {
  Serial.println("🔄 Resetting PN532...");
  
  // Re-initialize PN532
  nfc.begin();
  delay(100);
  
  if (!nfc.getFirmwareVersion()) {
    Serial.println("❌ PN532 reset failed!");
    pn532ErrorCount++;
    
    // Jika terlalu banyak error, restart ESP32
    if (pn532ErrorCount >= MAX_PN532_ERRORS) {
      Serial.println("🔴 Too many PN532 errors, restarting...");
      delay(1000);
      ESP.restart();
    }
    return;
  }
  
  nfc.SAMConfig();
  pn532ErrorCount = 0;
  cardStillPresent = false;
  
  Serial.println("✅ PN532 reset successful");
  playBeep(1500, 50);
}

/* =========================
   SERVER MODE FUNCTIONS
========================= */

void forceAttendanceModeOnServer() {
  if (WiFi.status() != WL_CONNECTED) return;
  
  Serial.println("🔄 Forcing attendance mode on server (startup reset)...");
  
  InsecureWiFiClient client;
  client.setTimeout(3000);
  
  HTTPClient http;
  
  char modeUrl[128];
  snprintf(modeUrl, sizeof(modeUrl), "%s%s/mode", serverURL, apiEndpoint.c_str());
  
  http.begin(client, modeUrl);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(3000);
  
  int httpResponseCode = http.POST("{\"mode\":\"attendance\"}");
  
  if (httpResponseCode == 200) {
    Serial.println("✅ Server mode reset to attendance on startup");
  } else {
    Serial.printf("⚠️ Failed to reset server mode, code: %d\n", httpResponseCode);
  }
  
  http.end();
  client.stop();
  
  // Pastikan local state juga attendance
  serverMode = "attendance";
  currentMode = MODE_ATTENDANCE;
  
  feedWatchdog();
}

void checkModeFromServer() {
  if (WiFi.status() != WL_CONNECTED) return;

  Serial.println("🔍 Checking mode from server...");
  
  InsecureWiFiClient client;
  client.setTimeout(3000);
  
  HTTPClient http;
  
  char modeUrl[128];
  snprintf(modeUrl, sizeof(modeUrl), "%s%s/mode", serverURL, apiEndpoint.c_str());
  
  http.begin(client, modeUrl);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(3000);
  
  int httpResponseCode = http.GET();
  
  if (httpResponseCode == 200) {
    String response = http.getString();
    
    DynamicJsonDocument doc(256);
    DeserializationError error = deserializeJson(doc, response);
    
    if (!error && doc["success"] == true) {
      String newMode = doc["mode"].as<String>();
      
      if (newMode != serverMode) {
        String oldMode = serverMode;
        serverMode = newMode;
        
        Serial.println("🔄 Server mode berubah: " + oldMode + " → " + serverMode);
        
        // PENTING: Ubah currentMode sesuai serverMode
        if (serverMode == "attendance" && currentMode != MODE_ATTENDANCE) {
          currentMode = MODE_ATTENDANCE;
          Serial.println("✅ Switching to ATTENDANCE MODE");
          
          // Clear tempUID saat kembali ke attendance
          tempUID = "";
          inputMode = false;
          currentInput = "";
          
        } else if (serverMode == "register" && currentMode != MODE_REGISTER) {
          currentMode = MODE_REGISTER;
          Serial.println("✅ Switching to REGISTER MODE (dari server)");
          
          // PENTING: Clear tempUID saat masuk register mode
          // Agar tidak menggunakan UID lama dari attendance
          tempUID = "";
          inputMode = false;
          currentInput = "";
          lastKey = 0;
          previewChar = "";
          
          // Clear UID cache di server juga
          clearUIDCache();
          
          Serial.println("🔄 tempUID cleared - waiting for new card tap");
        }
        
        // Update tampilan
        statusMessage = "Mode: " + serverMode;
        statusMessageTime = millis();
        displayNeedsUpdate = true;
        
        // Beep notification
        playBeep(2000, 100);
      }
    }
    
    response = String();
  } else {
    Serial.printf("❌ Mode check gagal, code: %d\n", httpResponseCode);
  }
  
  http.end();
  client.stop();
  
  feedWatchdog();
}

/* =========================
   SERVER DATA FUNCTIONS
========================= */

void sendUIDToForm(String uid) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("❌ WiFi not connected, cannot send UID to form");
    return;
  }

  Serial.printf("📤 Sending UID to form: %s\n", uid.c_str());

  InsecureWiFiClient client;
  client.setTimeout(2000);
  
  HTTPClient http;
  
  String url = String(serverURL) + apiEndpoint + "/register-uid";
  
  http.begin(client, url);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(2000);
  
  DynamicJsonDocument doc(128);
  doc["uid"] = uid;
  
  String jsonString;
  serializeJson(doc, jsonString);
  
  Serial.print("📦 JSON: ");
  Serial.println(jsonString);
  
  int httpResponseCode = http.POST(jsonString);
  
  Serial.printf("📡 HTTP Response code: %d\n", httpResponseCode);
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.print("📨 Response: ");
    Serial.println(response);
  }
  
  http.end();
  client.stop();
  feedWatchdog();
}

void clearUIDCache() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("⚠️ WiFi not connected, cannot clear UID cache");
    return;
  }

  Serial.println("🧹 Clearing UID cache on server...");

  InsecureWiFiClient client;
  client.setTimeout(2000);
  
  HTTPClient http;
  
  String url = String(serverURL) + apiEndpoint + "/clear-uid";
  
  http.begin(client, url);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(2000);
  
  int httpResponseCode = http.POST("");
  
  if (httpResponseCode == 200) {
    Serial.println("✅ UID cache cleared on server");
  } else {
    Serial.printf("⚠️ Failed to clear UID cache, code: %d\n", httpResponseCode);
  }
  
  http.end();
  client.stop();
  feedWatchdog();
}

bool sendDataToServer(String uid, String name, String type, bool isOfflineRetry) {
  if (WiFi.status() != WL_CONNECTED) {
    return false;
  }

  InsecureWiFiClient client;
  client.setTimeout(3000); // Timeout lebih pendek
  
  HTTPClient http;
  
  // Gunakan char array untuk URL
  char url[128];
  snprintf(url, sizeof(url), "%s%s/card-detected", serverURL, apiEndpoint.c_str());
  
  http.begin(client, url);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(3000);
  
  DynamicJsonDocument doc(512);
  doc["uid"] = uid;
  doc["mode"] = type;
  
  if (type == "register" && name.length() > 0) {
    doc["name"] = name;
  }
  
  if (isOfflineRetry) {
    doc["offline_retry"] = true;
  }
  
  String jsonString;
  serializeJson(doc, jsonString);
  
  int httpResponseCode = http.POST(jsonString);
  bool success = false;
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    
    Serial.println("📨 Server response:");
    Serial.println(response);
    
    DynamicJsonDocument responseDoc(1024);
    DeserializationError error = deserializeJson(responseDoc, response);
    
    if (!error && responseDoc["success"] == true) {
      Serial.println("✅ Response parsed successfully");
      
      // Ambil data karyawan dari response
      if (responseDoc.containsKey("employee")) {
        lastEmployeeName = responseDoc["employee"]["name"].as<String>();
        Serial.println("👤 Employee name: " + lastEmployeeName);
      } else {
        Serial.println("⚠️ No employee data in response");
      }
      
      if (responseDoc.containsKey("time")) {
        lastTapTime = responseDoc["time"].as<String>();
        Serial.println("⏰ Time: " + lastTapTime);
      }
      
      if (responseDoc.containsKey("type")) {
        String tapType = responseDoc["type"].as<String>();
        lastTapTime = tapType + " " + lastTapTime;
        Serial.println("📋 Type: " + tapType);
      }
      
      lastTapDisplay = millis();
      Serial.println("✅ Display data set, will show for 2 seconds");
      success = true;
    } else {
      Serial.println("❌ Failed to parse response or success=false");
      if (error) {
        Serial.print("JSON error: ");
        Serial.println(error.c_str());
      }
    }
    
    // Cleanup
    response = String();
  } else {
    Serial.printf("❌ HTTP error code: %d\n", httpResponseCode);
  }
  
  http.end();
  client.stop();
  
  // Feed watchdog
  feedWatchdog();
  
  return success;
}

/* =========================
   OFFLINE QUEUE FUNCTIONS
========================= */

void saveToOfflineQueue(String uid, String name, String type) {
  Serial.println("💾 Menyimpan data offline...");
  
  // Cek duplikat (UID yang sama dalam 5 detik)
  for (const auto& data : offlineQueue) {
    if (data.uid == uid && millis() - data.timestamp < 5000) {
      Serial.println("⚠️ Data duplikat, diabaikan");
      return;
    }
  }
  
  // Hapus data terlama jika queue penuh
  if (offlineQueue.size() >= MAX_OFFLINE_QUEUE) {
    offlineQueue.erase(offlineQueue.begin());
  }
  
  OfflineData data;
  data.uid = uid;
  data.name = name;
  data.type = type;
  data.timestamp = millis();
  data.retryCount = 0;
  
  offlineQueue.push_back(data);
  
  // Simpan ke Preferences (sederhana, hanya jumlah)
  preferences.putInt("offline_count", offlineQueue.size());
  
  Serial.printf("✅ Data tersimpan. Total offline: %d\n", offlineQueue.size());
}

void processOfflineQueue() {
  if (WiFi.status() != WL_CONNECTED || offlineQueue.size() == 0) return;
  
  // Mulai pengiriman jika tidak sedang mengirim
  if (!isSendingOffline) {
    if (millis() - lastOfflineSendAttempt >= OFFLINE_SEND_INTERVAL) {
      isSendingOffline = true;
      lastOfflineSendAttempt = millis();
      
      Serial.println("\n📤 Memulai pengiriman offline data...");
      Serial.printf("📊 Total data: %d\n", offlineQueue.size());
      
      sendNextOfflineData();
    }
  }
}

void sendNextOfflineData() {
  // Feed watchdog
  feedWatchdog();
  
  // Cari data yang belum pernah dikirim atau gagal
  int nextIndex = -1;
  for (size_t i = 0; i < offlineQueue.size(); i++) {
    if (offlineQueue[i].retryCount < MAX_RETRY_COUNT) {
      nextIndex = i;
      break;
    }
  }
  
  // Hapus data yang sudah melebihi max retry
  if (nextIndex == -1) {
    for (int i = offlineQueue.size() - 1; i >= 0; i--) {
      if (offlineQueue[i].retryCount >= MAX_RETRY_COUNT) {
        Serial.printf("🗑️ Menghapus data gagal (UID: %s)\n", 
                      offlineQueue[i].uid.c_str());
        offlineQueue.erase(offlineQueue.begin() + i);
      }
    }
    preferences.putInt("offline_count", offlineQueue.size());
    isSendingOffline = false;
    return;
  }
  
  currentSendingIndex = nextIndex;
  
  Serial.printf("\n📤 Mengirim data %d/%d (Percobaan ke-%d)\n", 
                nextIndex + 1, offlineQueue.size(), 
                offlineQueue[nextIndex].retryCount + 1);
  Serial.println("UID: " + offlineQueue[nextIndex].uid);
  
  bool sendSuccess = sendDataToServer(
    offlineQueue[nextIndex].uid,
    offlineQueue[nextIndex].name,
    offlineQueue[nextIndex].type,
    true
  );
  
  if (sendSuccess) {
    Serial.printf("✅ Data %d berhasil dikirim\n", nextIndex + 1);
    offlineQueue.erase(offlineQueue.begin() + nextIndex);
    preferences.putInt("offline_count", offlineQueue.size());
    
    playBeep(1500, 50);
    
    // PENTING: Jangan rekursi, set flag untuk kirim berikutnya di loop
    isSendingOffline = false;
    
  } else {
    offlineQueue[nextIndex].retryCount++;
    Serial.printf("❌ Data %d gagal (percobaan %d/%d)\n", 
                  nextIndex + 1, 
                  offlineQueue[nextIndex].retryCount, 
                  MAX_RETRY_COUNT);
    
    preferences.putInt("offline_count", offlineQueue.size());
    
    if (offlineQueue[nextIndex].retryCount >= MAX_RETRY_COUNT) {
      // Gagal permanen, hapus
      Serial.printf("🗑️ Menghapus data gagal permanen\n");
      offlineQueue.erase(offlineQueue.begin() + nextIndex);
      preferences.putInt("offline_count", offlineQueue.size());
    }
    
    // Set flag untuk retry di loop berikutnya
    isSendingOffline = false;
  }
}

/* =========================
   WIFI FUNCTIONS
========================= */

void connectToWiFi() {
  if (ssid.length() == 0) {
    statusMessage = "SSID empty";
    statusMessageTime = millis();
    return;
  }
  
  isConnecting = true;
  displayNeedsUpdate = true;
  
  WiFi.begin(ssid.c_str(), password.c_str());
  
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 30) {
    yield(); // Feed watchdog
    delay(500);
    attempts++;
    
    if (attempts % 2 == 0) {
      updateDisplay(true);
    }
    
    // Timeout protection
    if (attempts >= 30) {
      WiFi.disconnect();
      break;
    }
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    statusMessage = "Connected!";
    playBeep(2000, 200);
    preferences.putString("ssid", ssid);
    preferences.putString("password", password);
    currentMode = MODE_MAIN_MENU;
  } else {
    statusMessage = "Failed!";
    playBeep(500, 300);
  }
  
  isConnecting = false;
  statusMessageTime = millis();
  displayNeedsUpdate = true;
}

/* =========================
   ALARM FUNCTIONS
========================= */

void saveAlarmSettings() {
  preferences.putInt("masuk_jam", alarmMasukJam);
  preferences.putInt("masuk_menit", alarmMasukMenit);
  preferences.putInt("keluar_jam", alarmKeluarJam);
  preferences.putInt("keluar_menit", alarmKeluarMenit);
  preferences.putInt("istirahat_mulai_jam", alarmIstirahatMulaiJam);
  preferences.putInt("istirahat_mulai_menit", alarmIstirahatMulaiMenit);
  preferences.putInt("istirahat_selesai_jam", alarmIstirahatSelesaiJam);
  preferences.putInt("istirahat_selesai_menit", alarmIstirahatSelesaiMenit);
  Serial.println("Alarm settings saved");
}

void loadAlarmSettings() {
  alarmMasukJam = preferences.getInt("masuk_jam", 7);
  alarmMasukMenit = preferences.getInt("masuk_menit", 30);
  alarmKeluarJam = preferences.getInt("keluar_jam", 16);
  alarmKeluarMenit = preferences.getInt("keluar_menit", 0);
  alarmIstirahatMulaiJam = preferences.getInt("istirahat_mulai_jam", 12);
  alarmIstirahatMulaiMenit = preferences.getInt("istirahat_mulai_menit", 0);
  alarmIstirahatSelesaiJam = preferences.getInt("istirahat_selesai_jam", 13);
  alarmIstirahatSelesaiMenit = preferences.getInt("istirahat_selesai_menit", 0);
}

/* =========================
   WATCHDOG & MEMORY MANAGEMENT
========================= */

void feedWatchdog() {
  // Feed hardware watchdog timer
  esp_task_wdt_reset();
  yield();
  lastWatchdogFeed = millis();
  lastActivityTime = millis(); // Update activity time
}

void checkMemory() {
  size_t freeHeap = ESP.getFreeHeap();
  
  // Track minimum free heap
  if (freeHeap < minFreeHeap) {
    minFreeHeap = freeHeap;
  }
  
  // Log memory status
  Serial.printf("📊 Free Heap: %d bytes (Min: %d bytes)\n", freeHeap, minFreeHeap);
  
  // Warning jika memory rendah
  if (freeHeap < 20000) {
    Serial.println("⚠️ WARNING: Low memory!");
    
    // Cleanup jika perlu
    cleanupStrings();
    
    // Hapus data offline lama jika queue terlalu besar
    if (offlineQueue.size() > 30) {
      Serial.println("🧹 Cleaning old offline data...");
      offlineQueue.erase(offlineQueue.begin(), offlineQueue.begin() + 10);
      preferences.putInt("offline_count", offlineQueue.size());
    }
  }
  
  // Critical memory - restart
  if (freeHeap < 10000) {
    Serial.println("🔴 CRITICAL: Memory too low, restarting...");
    delay(1000);
    ESP.restart();
  }
}

void cleanupStrings() {
  // Cleanup string variables yang tidak digunakan
  if (statusMessage.length() > 0 && millis() - statusMessageTime > 5000) {
    statusMessage = String();
  }
  
  if (lastEmployeeName.length() > 0 && millis() - lastTapDisplay > TAP_DISPLAY_DURATION) {
    lastEmployeeName = String();
    lastTapTime = String();
  }
  
  if (previewChar.length() > 0 && millis() - lastKeyPress > KEY_TIMEOUT * 2) {
    previewChar = String();
  }
}

/* =========================
   RFID FUNCTIONS
========================= */

void checkRFID() {
  if (millis() - lastRFIDCheck < RFID_CHECK_INTERVAL) return;
  lastRFIDCheck = millis();
  
  // Check jika kartu masih ditempel terlalu lama
  if (cardStillPresent && millis() - cardPresentStart > CARD_PRESENT_TIMEOUT) {
    Serial.println("⚠️ Card held too long, forcing release...");
    
    // Warning beep
    playBeep(800, 100);
    delay(100);
    playBeep(800, 100);
    
    // Display warning - FORCE UPDATE
    statusMessage = "ANGKAT KARTU!";
    statusMessageTime = millis();
    displayNeedsUpdate = true;
    showAttendanceMode(); // Force show warning
    
    Serial.println("🚨 TIMEOUT! Forcing PN532 reset...");
    
    // Force reset PN532
    resetPN532();
    cardStillPresent = false;
    
    // Keep showing warning for 2 more seconds
    delay(2000);
    statusMessage = "";
    displayNeedsUpdate = true;
    return;
  }
  
  uint8_t uid[7];
  uint8_t uidLength;
  
  // Try to read card with timeout - LEBIH AGRESIF
  bool cardDetected = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength, 100);
  
  if (cardDetected) {
    // Kartu terdeteksi
    if (!cardStillPresent) {
      // Kartu baru terdeteksi (bukan yang sama)
      if (millis() - lastCardRead < CARD_READ_COOLDOWN) return;
      
      cardStillPresent = true;
      cardPresentStart = millis();
      lastCardRead = millis();
      
      Serial.println("🔔 NEW CARD DETECTED - PLEASE REMOVE!");
      
      String uidStr = "";
      uidStr.reserve(20);
      
      for (uint8_t i = 0; i < uidLength; i++) {
        if (uid[i] < 0x10) uidStr += "0";
        uidStr += String(uid[i], HEX);
      }
      uidStr.toUpperCase();
      
      Serial.print("RFID: ");
      Serial.println(uidStr);
      
      // Tap sound effect
      playTapSound();
      yield();
      
      // TAMPILKAN INDIKATOR "ANGKAT KARTU!" - SEGERA!
      statusMessage = "ANGKAT KARTU!";
      statusMessageTime = millis();
      displayNeedsUpdate = true;
      
      // Force update display SEKARANG
      showAttendanceMode();
      
      Serial.println("⚠️ ANGKAT KARTU! indicator shown");
      
      // Gunakan mode dari server untuk menentukan aksi
      if (serverMode == "attendance") {
        yield(); // Feed watchdog sebelum HTTP
        
        bool sendSuccess = sendDataToServer(uidStr, "", "attendance", false);
        
        yield(); // Feed watchdog setelah HTTP
        
        if (sendSuccess) {
          // Success melody (lebih menarik!)
          playSuccessMelody();
          yield();
          
          Serial.println("✅ Attendance recorded");
          
          // Clear "ANGKAT KARTU" message
          statusMessage = "";
          
          // PENTING: Force update display untuk menampilkan employee info
          displayNeedsUpdate = true;
          updateDisplay(true);
        } else {
          saveToOfflineQueue(uidStr, "", "attendance");
          statusMessage = "Offline saved";
          statusMessageTime = millis();
          playErrorMelody();
          yield();
          displayNeedsUpdate = true;
        }
        
      } else if (serverMode == "register") {
        tempUID = uidStr;
        
        // Kirim UID ke form Laravel
        sendUIDToForm(uidStr);
        
        statusMessage = "UID sent!";
        statusMessageTime = millis();
        displayNeedsUpdate = true;
        
        // Beep untuk register
        playBeep(1500, 100);
        yield();
        playBeep(1500, 100);
        yield();
        
        Serial.println("✅ UID sent to form");
        
        // PENTING: Setelah kartu terdeteksi di register mode,
        // kembali ke attendance mode dan halaman attendance
        Serial.println("🔄 Register card detected, switching back to ATTENDANCE mode");
        serverMode = "attendance";
        currentMode = MODE_ATTENDANCE;
        tempUID = "";
        inputMode = false;
        currentInput = "";
        lastKey = 0;
        previewChar = "";
        
        // Force update display ke attendance
        displayNeedsUpdate = true;
        updateDisplay(true);
      }
      
      // Cleanup
      uidStr = String();
      
      // Beep reminder untuk angkat kartu
      delay(500);
      playBeep(1200, 50);
    }
    // Kartu masih ditempel (cardStillPresent == true)
    // Tidak lakukan apa-apa, tunggu diangkat atau timeout
    
  } else {
    // Kartu tidak terdeteksi (sudah diangkat)
    if (cardStillPresent) {
      Serial.println("✅ Card removed");
      cardStillPresent = false;
      pn532ErrorCount = 0; // Reset error count
      
      // Clear "ANGKAT KARTU!" message
      if (statusMessage == "ANGKAT KARTU!") {
        statusMessage = "";
        displayNeedsUpdate = true;
      }
    }
  }
  
  // Feed watchdog
  feedWatchdog();
}

/* =========================
   SETUP
========================= */

void setup() {
  Serial.begin(115200);
  delay(1000);
  
  Serial.println("\n=== STARTING ATTENDANCE SYSTEM ===");
  
  // Enable hardware watchdog timer (30 seconds) - Compatible with newer ESP32 core
  // Check if already initialized
  esp_task_wdt_deinit(); // Deinit jika sudah ada
  
  esp_task_wdt_config_t wdt_config = {
    .timeout_ms = WDT_TIMEOUT * 1000,
    .idle_core_mask = 0,
    .trigger_panic = true
  };
  esp_task_wdt_init(&wdt_config);
  esp_task_wdt_add(NULL);
  Serial.println("✅ Hardware Watchdog enabled (30s timeout)");
  
  Wire.begin(SDA_PIN, SCL_PIN, 50000);
  Serial.println("I2C OK");
  feedWatchdog();
  
  tft.begin();
  tft.setRotation(1);
  tft.fillScreen(ILI9341_BLACK);
  tft.setTextColor(ILI9341_GREEN);
  tft.setCursor(40, 100);
  tft.print("SYSTEM READY");
  Serial.println("TFT OK");
  
  feedWatchdog();
  
  for (int i = 0; i < 3; i++) {
    keypad.begin();
    keypad.setHoldTime(200);
    keypad.setDebounceTime(20);
    delay(50);
  }
  Serial.println("Keypad OK");
  
  nfc.begin();
  if (!nfc.getFirmwareVersion()) {
    tft.fillScreen(ILI9341_RED);
    tft.setCursor(20,100);
    tft.print("PN532 ERROR");
    Serial.println("PN532 ERROR");
    while (1) delay(1000);
  }
  nfc.SAMConfig();
  Serial.println("PN532 OK");
  
  setupI2S();
  Serial.println("Audio OK");
  
  // Welcome melody (setelah I2S ready)
  playWelcomeMelody();
  
  preferences.begin("attendance", false);
  
  ssid = preferences.getString("ssid", "");
  password = preferences.getString("password", "");
  loadAlarmSettings();
  
  // Load offline queue count
  int offlineCount = preferences.getInt("offline_count", 0);
  if (offlineCount > 0) {
    Serial.printf("📂 Ada %d data offline tersimpan\n", offlineCount);
  }
  
  // Auto-connect WiFi saat startup
  if (ssid.length() > 0) {
    Serial.println("🔌 Auto-connecting to WiFi...");
    Serial.print("SSID: ");
    Serial.println(ssid);
    
    WiFi.mode(WIFI_STA);
    WiFi.setAutoReconnect(true);
    WiFi.setSleep(false);
    WiFi.begin(ssid.c_str(), password.c_str());
    
    // Tunggu koneksi maksimal 10 detik
    int attempts = 0;
    while (WiFi.status() != WL_CONNECTED && attempts < 20) {
      delay(500);
      Serial.print(".");
      attempts++;
    }
    
    if (WiFi.status() == WL_CONNECTED) {
      Serial.println("\n✅ WiFi Connected!");
      Serial.print("IP: ");
      Serial.println(WiFi.localIP());
      playBeep(2000, 100);
      
      // Sync time from server
      syncTimeFromServer();
      
      // PENTING: Saat startup, paksa mode ke attendance di server
      // Agar tidak stuck di register mode dari sesi sebelumnya
      forceAttendanceModeOnServer();
    } else {
      Serial.println("\n❌ WiFi Connection Failed");
      playBeep(500, 200);
    }
  }
  
  delay(1000);
  
  // Jika WiFi connected dan mode sudah di-set ke attendance,
  // langsung masuk ke attendance mode (skip main menu)
  if (WiFi.status() == WL_CONNECTED) {
    currentMode = MODE_ATTENDANCE;
    showAttendanceMode();
    Serial.println("📌 Auto-start ke ATTENDANCE mode");
  } else {
    showMainMenu();
  }
  
  playBeep(1500, 100);
  
  Serial.println("=== READY ===\n");
}

/* =========================
   LOOP
========================= */

void loop() {
  unsigned long now = millis();
  
  // Feed watchdog secara berkala (PRIORITAS TERTINGGI - SELALU AKTIF)
  if (now - lastWatchdogFeed > WATCHDOG_FEED_INTERVAL) {
    feedWatchdog();
  }
  
  // ========================================
  // PRIORITAS TERTINGGI: KEYPAD CHECK
  // Selalu aktif di semua mode untuk responsivitas maksimal
  // ========================================
  if (now - lastKeypadCheck >= KEYPAD_CHECK_INTERVAL) {
    checkKeypad();
    lastKeypadCheck = now;
  }
  
  // ========================================
  // MODE-SPECIFIC OPERATIONS
  // Fitur berat HANYA aktif di mode tertentu
  // ========================================
  
  // WiFi Setup & Main Menu: MINIMAL OPERATIONS (maksimal responsivitas keypad!)
  if (currentMode == MODE_WIFI_SETUP || currentMode == MODE_MAIN_MENU || 
      currentMode == MODE_ALARM_SETUP) {
    
    // HANYA update display jika ada perubahan
    if (displayNeedsUpdate && now - lastDisplayUpdate >= DISPLAY_UPDATE_INTERVAL) {
      lastDisplayUpdate = now;
      updateDisplay(false);
    }
    
    // TIDAK ADA operasi berat lainnya!
    // Tidak ada: RFID check, mode check, time sync, offline queue, dll
    // Semua CPU resource untuk keypad responsiveness!
    
    yield();
    return; // Exit loop early untuk maksimal speed
  }
  
  // ========================================
  // ATTENDANCE MODE: FULL FEATURES AKTIF
  // ========================================
  if (currentMode == MODE_ATTENDANCE) {
    
    // Update clock
    unsigned long prevSecond = currentSecond;
    updateClock();
    
    // Update jam di TFT hanya saat detik berubah (partial update)
    if (currentSecond != prevSecond) {
      tft.fillRect(85, 40, 150, 20, COLOR_DARK);
      drawClock(85, 40, 2);
    }
    
    // Update animation frame HANYA saat ada employee info
    if (lastEmployeeName.length() > 0) {
      if (now - lastAnimationUpdate > ANIMATION_INTERVAL) {
        lastAnimationUpdate = now;
        animationFrame++;
        if (animationFrame > 1000) animationFrame = 0;
        displayNeedsUpdate = true;
      }
    }
    
    // Check memory secara berkala
    if (now - lastMemoryCheck > MEMORY_CHECK_INTERVAL) {
      checkMemory();
      lastMemoryCheck = now;
    }
    
    // Anti-hang: Restart jika tidak ada aktivitas selama 1 jam
    if (now - lastActivityTime > ACTIVITY_TIMEOUT) {
      Serial.println("⏰ No activity for 1 hour, restarting for stability...");
      delay(1000);
      ESP.restart();
    }
    
    // WiFi reconnect check
    if (now - lastWiFiCheck > WIFI_CHECK_INTERVAL) {
      if (WiFi.status() != WL_CONNECTED && ssid.length() > 0) {
        Serial.println("📡 WiFi disconnected, reconnecting...");
        WiFi.disconnect();
        WiFi.begin(ssid.c_str(), password.c_str());
        consecutiveErrors++;
        
        if (consecutiveErrors > MAX_CONSECUTIVE_ERRORS) {
          Serial.println("� Too many errors, restarting...");
          delay(1000);
          ESP.restart();
        }
      } else {
        consecutiveErrors = 0;
      }
      lastWiFiCheck = now;
    }
    
    // Cek mode dari server (hanya jika WiFi connected)
    if (WiFi.status() == WL_CONNECTED) {
      if (now - lastModeCheck > MODE_CHECK_INTERVAL) {
        checkModeFromServer();
        lastModeCheck = now;
      }
      
      // Sync time setiap 1 jam
      if (now - lastTimeSync > TIME_SYNC_INTERVAL) {
        syncTimeFromServer();
        lastTimeSync = now;
      }
    }
    
    // Check RFID
    if (now - lastRFIDCheck >= RFID_CHECK_INTERVAL) {
      checkRFID();
      lastRFIDCheck = now;
    }
    
    // Auto-recovery PN532 jika terlalu banyak error
    if (pn532ErrorCount >= MAX_PN532_ERRORS) {
      Serial.println("🔄 Auto-recovering PN532...");
      resetPN532();
    }
    
    // Proses offline queue (non-blocking)
    processOfflineQueue();
    
    // Auto-clear employee info setelah 2 detik
    if (lastEmployeeName.length() > 0 && now - lastTapDisplay >= TAP_DISPLAY_DURATION) {
      lastEmployeeName = "";
      lastTapTime = "";
      slideOffset = 0;
      animationFrame = 0;
      displayNeedsUpdate = true;
    }
    
    // Auto-clear status message
    if (statusMessage.length() > 0 && now - statusMessageTime > 3000) {
      statusMessage = "";
      displayNeedsUpdate = true;
    }
    
    // Cleanup strings periodically
    cleanupStrings();
  }
  
  // ========================================
  // REGISTER MODE: MODERATE FEATURES
  // ========================================
  if (currentMode == MODE_REGISTER) {
    
    // Check RFID untuk register
    if (now - lastRFIDCheck >= RFID_CHECK_INTERVAL) {
      checkRFID();
      lastRFIDCheck = now;
    }
    
    // Proses offline queue jika ada
    if (WiFi.status() == WL_CONNECTED) {
      processOfflineQueue();
    }
    
    // Auto-clear status message
    if (statusMessage.length() > 0 && now - statusMessageTime > 3000) {
      statusMessage = "";
      displayNeedsUpdate = true;
    }
  }
  
  // Update display (semua mode)
  if (displayNeedsUpdate && now - lastDisplayUpdate >= DISPLAY_UPDATE_INTERVAL) {
    lastDisplayUpdate = now;
    updateDisplay(false);
  }
  
  // Reset keypad jika error
  if (errorCount > 10) {
    resetKeypad();
    errorCount = 0;
  }
  
  // Minimal delay untuk yield
  yield();
}