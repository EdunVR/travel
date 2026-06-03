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
#include <WebServer.h>
#include <SD_MMC.h>
#include <FS.h>
#include <EEPROM.h>
#include <time.h>

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
#define SAMPLE_RATE 16000
#define I2S_PORT I2S_NUM_0

// SD Card
#define SD_MMC_CLK 15
#define SD_MMC_CMD 16
#define SD_MMC_D0  17

// EEPROM Size
#define EEPROM_SIZE 128

/* =========================
   OBJECTS
========================= */

Adafruit_ILI9341 tft = Adafruit_ILI9341(TFT_CS, TFT_DC, TFT_RST);
Adafruit_PN532 nfc(-1, -1);
WebServer server(80);

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
   STRUCTURES & VARIABLES
========================= */

struct UserData {
  char uid[20];
  char name[32];
  bool registered;
};

struct AttendanceRecord {
  char uid[20];
  char name[32];
  char time[20];
  char date[20];
  char type[10];
};

struct AlarmTime {
  int jam;
  int menit;
};

// Mode operasi
enum Mode {
  MODE_REGISTER,
  MODE_ATTENDANCE,
  MODE_SETTINGS,
  MODE_WIFI_SETUP,
  MODE_ALARM_SETUP
};

Mode currentMode = MODE_ATTENDANCE;
bool isSettingWiFi = false;
bool isSettingAlarm = false;

// Variabel WiFi
String ssid = "";
String password = "";
char wifiInput[32] = "";
int wifiInputIndex = 0;
bool enteringSSID = true;

// Variabel alarm
AlarmTime jamMasuk = {8, 0};
AlarmTime istirahatMulai = {12, 0};
AlarmTime istirahatSelesai = {13, 0};
AlarmTime jamPulang = {17, 0};

// Variabel input keypad (multi-tap)
char lastKey = 0;
unsigned long lastKeyPress = 0;
int keyPressCount = 0;
String inputBuffer = "";
bool inputMode = false;
String currentInput = "";

// Map untuk multi-tap keypad (seperti HP jaman dulu)
const char* keyMap[] = {
  ".,?!1",  // 1
  "abc2",   // 2
  "def3",   // 3
  "ghi4",   // 4
  "jkl5",   // 5
  "mno6",   // 6
  "pqrs7",  // 7
  "tuv8",   // 8
  "wxyz9",  // 9
  "*",      // *
  " 0",     // 0
  "#"       // #
};

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
  const int bufferSize = 256;
  int16_t buffer[bufferSize];

  int totalSamples = (SAMPLE_RATE * duration_ms) / 1000;
  int generated = 0;

  i2s_start(I2S_PORT);

  while (generated < totalSamples) {
    for (int i = 0; i < bufferSize; i++) {
      float t = (generated + i) / (float)SAMPLE_RATE;
      buffer[i] = 8000 * sin(2 * PI * freq * t);
    }

    size_t bytes_written;
    i2s_write(I2S_PORT, buffer, bufferSize * sizeof(int16_t),
              &bytes_written, portMAX_DELAY);

    generated += bufferSize;
  }

  int16_t silence[256] = {0};
  for (int i = 0; i < 4; i++) {
    size_t bytes_written;
    i2s_write(I2S_PORT, silence, sizeof(silence),
              &bytes_written, portMAX_DELAY);
  }

  i2s_zero_dma_buffer(I2S_PORT);
  i2s_stop(I2S_PORT);
}

void playIndonesianAlarm(const char* message) {
  // Simple morse code pattern untuk alarm
  // (Dalam implementasi nyata, Anda bisa menggunakan file WAV)
  playBeep(1000, 200);
  delay(100);
  playBeep(1200, 200);
  delay(100);
  playBeep(1400, 200);
  delay(300);
  playBeep(1000, 400);
}

/* =========================
   SD CARD FUNCTIONS
========================= */

bool initSDCard() {
  if (!SD_MMC.begin()) {
    Serial.println("SD Card mount failed");
    return false;
  }
  
  uint8_t cardType = SD_MMC.cardType();
  if (cardType == CARD_NONE) {
    Serial.println("No SD Card attached");
    return false;
  }
  
  return true;
}

void saveAttendance(String uid, String name, String type) {
  File file = SD_MMC.open("/attendance.csv", FILE_APPEND);
  if (!file) {
    Serial.println("Failed to open file");
    return;
  }
  
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) {
    Serial.println("Failed to obtain time");
    return;
  }
  
  char timeStr[20];
  char dateStr[20];
  strftime(timeStr, sizeof(timeStr), "%H:%M:%S", &timeinfo);
  strftime(dateStr, sizeof(dateStr), "%Y-%m-%d", &timeinfo);
  
  file.printf("%s,%s,%s,%s,%s\n", 
              uid.c_str(), 
              name.c_str(), 
              dateStr, 
              timeStr, 
              type.c_str());
  file.close();
  
  Serial.println("Attendance saved to SD card");
}

void saveUser(String uid, String name) {
  File file = SD_MMC.open("/users.csv", FILE_APPEND);
  if (!file) {
    Serial.println("Failed to open users file");
    return;
  }
  
  file.printf("%s,%s\n", uid.c_str(), name.c_str());
  file.close();
  
  Serial.println("User saved to SD card");
}

String getUserName(String uid) {
  File file = SD_MMC.open("/users.csv");
  if (!file) {
    return "Unknown";
  }
  
  while (file.available()) {
    String line = file.readStringUntil('\n');
    int commaIndex = line.indexOf(',');
    if (commaIndex > 0) {
      String fileUid = line.substring(0, commaIndex);
      if (fileUid == uid) {
        file.close();
        return line.substring(commaIndex + 1);
      }
    }
  }
  file.close();
  return "Unknown";
}

/* =========================
   WIFI FUNCTIONS
========================= */

void connectToWiFi() {
  if (ssid.length() > 0) {
    tft.fillScreen(ILI9341_BLACK);
    tft.setCursor(10, 50);
    tft.setTextColor(ILI9341_WHITE);
    tft.print("Connecting to WiFi...");
    tft.setCursor(10, 80);
    tft.print("SSID: " + ssid);
    
    WiFi.begin(ssid.c_str(), password.c_str());
    
    int attempts = 0;
    while (WiFi.status() != WL_CONNECTED && attempts < 20) {
      delay(500);
      tft.print(".");
      attempts++;
    }
    
    if (WiFi.status() == WL_CONNECTED) {
      tft.setCursor(10, 120);
      tft.setTextColor(ILI9341_GREEN);
      tft.print("Connected!");
      tft.setCursor(10, 150);
      tft.print("IP: " + WiFi.localIP().toString());
      
      // Config time
      configTime(7 * 3600, 0, "pool.ntp.org", "time.nist.gov");
      
      playBeep(2000, 200);
    } else {
      tft.setCursor(10, 120);
      tft.setTextColor(ILI9341_RED);
      tft.print("Failed to connect");
    }
    
    delay(2000);
  }
}

/* =========================
   WEBSERVER FUNCTIONS
========================= */

void handleRoot() {
  String html = "<!DOCTYPE html><html>";
  html += "<head><title>Attendance System</title>";
  html += "<meta name='viewport' content='width=device-width, initial-scale=1'>";
  html += "<style>body{font-family:Arial;margin:20px;}</style>";
  html += "</head><body>";
  html += "<h1>Attendance System</h1>";
  html += "<ul>";
  html += "<li><a href='/records'>View Attendance Records</a></li>";
  html += "<li><a href='/users'>View Registered Users</a></li>";
  html += "</ul>";
  html += "</body></html>";
  
  server.send(200, "text/html", html);
}

void handleRecords() {
  String html = "<!DOCTYPE html><html>";
  html += "<head><title>Attendance Records</title>";
  html += "<meta name='viewport' content='width=device-width, initial-scale=1'>";
  html += "<style>table{border-collapse:collapse;width:100%;}";
  html += "th,td{border:1px solid #ddd;padding:8px;text-align:left;}";
  html += "th{background-color:#4CAF50;color:white;}</style>";
  html += "</head><body>";
  html += "<h1>Attendance Records</h1>";
  html += "<table><tr><th>UID</th><th>Name</th><th>Date</th><th>Time</th><th>Type</th></tr>";
  
  File file = SD_MMC.open("/attendance.csv");
  if (file) {
    while (file.available()) {
      String line = file.readStringUntil('\n');
      html += "<tr>";
      int commaIndex = 0;
      int lastComma = 0;
      int fieldCount = 0;
      
      while (commaIndex >= 0 && fieldCount < 5) {
        commaIndex = line.indexOf(',', lastComma);
        String field;
        if (commaIndex >= 0) {
          field = line.substring(lastComma, commaIndex);
          lastComma = commaIndex + 1;
        } else {
          field = line.substring(lastComma);
          field.trim();
        }
        html += "<td>" + field + "</td>";
        fieldCount++;
      }
      html += "</tr>";
    }
    file.close();
  }
  
  html += "</table><br><a href='/'>Back</a>";
  html += "</body></html>";
  
  server.send(200, "text/html", html);
}

void handleUsers() {
  String html = "<!DOCTYPE html><html>";
  html += "<head><title>Registered Users</title>";
  html += "<meta name='viewport' content='width=device-width, initial-scale=1'>";
  html += "<style>table{border-collapse:collapse;width:100%;}";
  html += "th,td{border:1px solid #ddd;padding:8px;text-align:left;}";
  html += "th{background-color:#4CAF50;color:white;}</style>";
  html += "</head><body>";
  html += "<h1>Registered Users</h1>";
  html += "<table><tr><th>UID</th><th>Name</th></tr>";
  
  File file = SD_MMC.open("/users.csv");
  if (file) {
    while (file.available()) {
      String line = file.readStringUntil('\n');
      int commaIndex = line.indexOf(',');
      if (commaIndex > 0) {
        String uid = line.substring(0, commaIndex);
        String name = line.substring(commaIndex + 1);
        name.trim();
        html += "<tr><td>" + uid + "</td><td>" + name + "</td></tr>";
      }
    }
    file.close();
  }
  
  html += "</table><br><a href='/'>Back</a>";
  html += "</body></html>";
  
  server.send(200, "text/html", html);
}

/* =========================
   DISPLAY FUNCTIONS
========================= */

void showMainMenu() {
  tft.fillScreen(ILI9341_BLACK);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(2);
  tft.setCursor(20, 20);
  tft.print("ATTENDANCE SYSTEM");
  
  tft.setTextSize(1);
  tft.setCursor(20, 60);
  tft.print("1. Attendance Mode");
  tft.setCursor(20, 80);
  tft.print("2. Register Mode");
  tft.setCursor(20, 100);
  tft.print("3. WiFi Setup");
  tft.setCursor(20, 120);
  tft.print("4. Alarm Setup");
  
  tft.setTextColor(ILI9341_YELLOW);
  tft.setCursor(20, 160);
  tft.print("Current: ");
  if (currentMode == MODE_ATTENDANCE) tft.print("Attendance");
  else if (currentMode == MODE_REGISTER) tft.print("Register");
  else if (currentMode == MODE_WIFI_SETUP) tft.print("WiFi Setup");
  else if (currentMode == MODE_ALARM_SETUP) tft.print("Alarm Setup");
  
  if (WiFi.status() == WL_CONNECTED) {
    tft.setTextColor(ILI9341_GREEN);
    tft.setCursor(20, 190);
    tft.print("WiFi Connected");
    tft.setCursor(20, 205);
    tft.print(WiFi.localIP().toString());
  }
}

void showWiFiSetup() {
  tft.fillScreen(ILI9341_BLACK);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(2);
  tft.setCursor(20, 20);
  tft.print("WiFi SETUP");
  
  tft.setTextSize(1);
  tft.setCursor(20, 60);
  tft.print("Enter SSID:");
  tft.setCursor(20, 80);
  
  if (enteringSSID) {
    tft.print(ssid + "_");
  } else {
    tft.print(ssid);
    tft.setCursor(20, 100);
    tft.print("Enter Password:");
    tft.setCursor(20, 120);
    tft.print(password + "_");
  }
  
  tft.setTextColor(ILI9341_YELLOW);
  tft.setCursor(20, 160);
  tft.print("Press # to continue");
  tft.setCursor(20, 180);
  tft.print("Press * to delete");
}

void showAlarmSetup() {
  tft.fillScreen(ILI9341_BLACK);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(2);
  tft.setCursor(20, 20);
  tft.print("ALARM SETUP");
  
  tft.setTextSize(1);
  tft.setCursor(20, 60);
  tft.print("1. Jam Masuk: ");
  tft.printf("%02d:%02d\n", jamMasuk.jam, jamMasuk.menit);
  
  tft.setCursor(20, 80);
  tft.print("2. Istirahat Mulai: ");
  tft.printf("%02d:%02d\n", istirahatMulai.jam, istirahatMulai.menit);
  
  tft.setCursor(20, 100);
  tft.print("3. Istirahat Selesai: ");
  tft.printf("%02d:%02d\n", istirahatSelesai.jam, istirahatSelesai.menit);
  
  tft.setCursor(20, 120);
  tft.print("4. Jam Pulang: ");
  tft.printf("%02d:%02d\n", jamPulang.jam, jamPulang.menit);
  
  tft.setTextColor(ILI9341_YELLOW);
  tft.setCursor(20, 160);
  tft.print("Press A to save");
  tft.setCursor(20, 180);
  tft.print("Press # to back");
}

void showRegistrationScreen() {
  tft.fillScreen(ILI9341_BLACK);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(2);
  tft.setCursor(20, 20);
  tft.print("REGISTER MODE");
  
  tft.setTextSize(1);
  tft.setCursor(20, 60);
  tft.print("Tap your card...");
  
  if (inputMode) {
    tft.setCursor(20, 100);
    tft.print("Enter Name:");
    tft.setCursor(20, 120);
    tft.print(currentInput + "_");
  }
}

/* =========================
   KEYPAD MULTI-TAP FUNCTIONS
========================= */

void handleKeypadInput(char key) {
  unsigned long now = millis();
  
  // Menu navigation
  if (!inputMode) {
    if (key == '1') {
      currentMode = MODE_ATTENDANCE;
      showMainMenu();
    } else if (key == '2') {
      currentMode = MODE_REGISTER;
      showRegistrationScreen();
    } else if (key == '3') {
      currentMode = MODE_WIFI_SETUP;
      isSettingWiFi = true;
      enteringSSID = true;
      ssid = "";
      password = "";
      showWiFiSetup();
    } else if (key == '4') {
      currentMode = MODE_ALARM_SETUP;
      showAlarmSetup();
    } else if (key == '#') {
      currentMode = MODE_SETTINGS;
      showMainMenu();
    }
    return;
  }
  
  // Input mode (multi-tap)
  if (now - lastKeyPress > 1000) {
    // Timeout, apply current character
    if (lastKey != 0 && keyPressCount > 0) {
      int keyIndex = lastKey - '1';
      if (keyIndex >= 0 && keyIndex <= 9) {
        const char* chars = keyMap[keyIndex];
        int len = strlen(chars);
        int charIndex = (keyPressCount - 1) % len;
        currentInput += chars[charIndex];
      } else if (lastKey == '0') {
        currentInput += ' ';
      }
      keyPressCount = 0;
      lastKey = 0;
    }
  }
  
  if (key == lastKey) {
    // Same key pressed
    keyPressCount++;
  } else {
    // New key pressed
    if (lastKey != 0 && keyPressCount > 0) {
      int keyIndex = lastKey - '1';
      if (keyIndex >= 0 && keyIndex <= 9) {
        const char* chars = keyMap[keyIndex];
        int len = strlen(chars);
        int charIndex = (keyPressCount - 1) % len;
        currentInput += chars[charIndex];
      } else if (lastKey == '0') {
        currentInput += ' ';
      }
    }
    
    lastKey = key;
    keyPressCount = 1;
  }
  
  if (key == '*') {
    // Delete last character
    if (currentInput.length() > 0) {
      currentInput.remove(currentInput.length() - 1);
    }
    keyPressCount = 0;
    lastKey = 0;
  } else if (key == '#') {
    // Confirm input
    inputMode = false;
    if (isSettingWiFi) {
      if (enteringSSID) {
        ssid = currentInput;
        enteringSSID = false;
        currentInput = "";
        showWiFiSetup();
      } else {
        password = currentInput;
        isSettingWiFi = false;
        inputMode = false;
        connectToWiFi();
        currentMode = MODE_SETTINGS;
        showMainMenu();
      }
    }
  }
  
  lastKeyPress = now;
  
  // Update display
  if (currentMode == MODE_WIFI_SETUP) {
    showWiFiSetup();
  } else if (currentMode == MODE_REGISTER && inputMode) {
    showRegistrationScreen();
  }
}

/* =========================
   ALARM CHECK FUNCTION
========================= */

void checkAlarm() {
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) {
    return;
  }
  
  static int lastCheckedHour = -1;
  static int lastCheckedMinute = -1;
  
  if (timeinfo.tm_hour != lastCheckedHour || timeinfo.tm_min != lastCheckedMinute) {
    lastCheckedHour = timeinfo.tm_hour;
    lastCheckedMinute = timeinfo.tm_min;
    
    // Check each alarm time
    if (timeinfo.tm_hour == jamMasuk.jam && timeinfo.tm_min == jamMasuk.menit) {
      playIndonesianAlarm("Jam Masuk");
      tft.fillScreen(ILI9341_GREEN);
      tft.setCursor(20, 100);
      tft.setTextColor(ILI9341_BLACK);
      tft.setTextSize(2);
      tft.print("WAKTU MASUK");
      delay(2000);
      showMainMenu();
    }
    
    if (timeinfo.tm_hour == istirahatMulai.jam && timeinfo.tm_min == istirahatMulai.menit) {
      playIndonesianAlarm("Istirahat Mulai");
      tft.fillScreen(ILI9341_YELLOW);
      tft.setCursor(20, 100);
      tft.setTextColor(ILI9341_BLACK);
      tft.setTextSize(2);
      tft.print("ISTIRAHAT MULAI");
      delay(2000);
      showMainMenu();
    }
    
    if (timeinfo.tm_hour == istirahatSelesai.jam && timeinfo.tm_min == istirahatSelesai.menit) {
      playIndonesianAlarm("Istirahat Selesai");
      tft.fillScreen(ILI9341_BLUE);
      tft.setCursor(20, 100);
      tft.setTextColor(ILI9341_WHITE);
      tft.setTextSize(2);
      tft.print("ISTIRAHAT SELESAI");
      delay(2000);
      showMainMenu();
    }
    
    if (timeinfo.tm_hour == jamPulang.jam && timeinfo.tm_min == jamPulang.menit) {
      playIndonesianAlarm("Jam Pulang");
      tft.fillScreen(ILI9341_RED);
      tft.setCursor(20, 100);
      tft.setTextColor(ILI9341_WHITE);
      tft.setTextSize(2);
      tft.print("WAKTU PULANG");
      delay(2000);
      showMainMenu();
    }
  }
}

/* =========================
   SETUP
========================= */

void setup() {
  Serial.begin(115200);
  delay(1500);

  // I2C
  Wire.begin(SDA_PIN, SCL_PIN);

  // TFT
  tft.begin();
  tft.setRotation(1);
  tft.fillScreen(ILI9341_BLACK);
  tft.setTextColor(ILI9341_GREEN);
  tft.setTextSize(2);
  tft.setCursor(30, 100);
  tft.print("SYSTEM READY");

  // Keypad
  keypad.begin();

  // PN532
  nfc.begin();
  if (!nfc.getFirmwareVersion()) {
    tft.fillScreen(ILI9341_RED);
    tft.setCursor(10,100);
    tft.setTextSize(1);
    tft.print("PN532 ERROR");
    while (1);
  }
  nfc.SAMConfig();

  // Audio
  setupI2S();

  // SD Card
  if (!initSDCard()) {
    tft.setCursor(10, 150);
    tft.setTextColor(ILI9341_YELLOW);
    tft.print("SD Card Error");
  }

  // EEPROM
  EEPROM.begin(EEPROM_SIZE);
  
  // Load alarm settings from EEPROM (simplified)
  jamMasuk.jam = EEPROM.read(0);
  jamMasuk.menit = EEPROM.read(1);
  istirahatMulai.jam = EEPROM.read(2);
  istirahatMulai.menit = EEPROM.read(3);
  istirahatSelesai.jam = EEPROM.read(4);
  istirahatSelesai.menit = EEPROM.read(5);
  jamPulang.jam = EEPROM.read(6);
  jamPulang.menit = EEPROM.read(7);
  
  // Load WiFi credentials (simplified)
  // In production, you'd want better storage
  
  delay(2000);
  showMainMenu();
  
  playBeep(1500, 200);
  
  Serial.println("ALL SYSTEM READY");
}

/* =========================
   LOOP
========================= */

void loop() {
  // ===== WEBSERVER =====
  server.handleClient();
  
  // ===== ALARM CHECK =====
  if (WiFi.status() == WL_CONNECTED) {
    checkAlarm();
  }
  
  // ===== KEYPAD =====
  char key = keypad.getKey();
  if (key) {
    Serial.print("Keypad: ");
    Serial.println(key);
    
    // Handle menu navigation and input
    if (currentMode == MODE_SETTINGS) {
      if (key >= '1' && key <= '4') {
        if (key == '1') {
          currentMode = MODE_ATTENDANCE;
          showMainMenu();
        } else if (key == '2') {
          currentMode = MODE_REGISTER;
          showRegistrationScreen();
        } else if (key == '3') {
          currentMode = MODE_WIFI_SETUP;
          isSettingWiFi = true;
          enteringSSID = true;
          ssid = "";
          password = "";
          showWiFiSetup();
        } else if (key == '4') {
          currentMode = MODE_ALARM_SETUP;
          showAlarmSetup();
        }
      }
    } else if (currentMode == MODE_WIFI_SETUP) {
      if (!inputMode && key == '#') {
        inputMode = true;
        currentInput = "";
        showWiFiSetup();
      } else if (inputMode) {
        handleKeypadInput(key);
      }
    } else if (currentMode == MODE_ALARM_SETUP) {
      if (key == 'A') {
        // Save alarm settings
        EEPROM.write(0, jamMasuk.jam);
        EEPROM.write(1, jamMasuk.menit);
        EEPROM.write(2, istirahatMulai.jam);
        EEPROM.write(3, istirahatMulai.menit);
        EEPROM.write(4, istirahatSelesai.jam);
        EEPROM.write(5, istirahatSelesai.menit);
        EEPROM.write(6, jamPulang.jam);
        EEPROM.write(7, jamPulang.menit);
        EEPROM.commit();
        
        tft.fillScreen(ILI9341_GREEN);
        tft.setCursor(30, 100);
        tft.setTextColor(ILI9341_BLACK);
        tft.print("SAVED!");
        delay(1000);
        currentMode = MODE_SETTINGS;
        showMainMenu();
      } else if (key == '#') {
        currentMode = MODE_SETTINGS;
        showMainMenu();
      }
      // In production, add number input to change times
    }
    
    playBeep(1000, 100);
  }

  // ===== RFID (non blocking) =====
  uint8_t uid[7];
  uint8_t uidLength;

  if (nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength, 50)) {
    
    // Convert UID to string
    String uidStr = "";
    for (uint8_t i = 0; i < uidLength; i++) {
      if (uid[i] < 0x10) uidStr += "0";
      uidStr += String(uid[i], HEX);
    }
    uidStr.toUpperCase();
    
    Serial.print("UID: ");
    Serial.println(uidStr);
    
    tft.fillScreen(ILI9341_BLUE);
    tft.setCursor(10, 40);
    tft.setTextColor(ILI9341_WHITE);
    tft.setTextSize(1);
    tft.print("RFID DETECTED");
    tft.setCursor(10, 60);
    tft.print("UID: " + uidStr);
    
    if (currentMode == MODE_REGISTER) {
      // Register mode
      tft.setCursor(10, 90);
      tft.print("Enter name:");
      
      inputMode = true;
      currentInput = "";
      
      // Wait for name input
      unsigned long timeout = millis() + 30000; // 30 second timeout
      while (inputMode && millis() < timeout) {
        key = keypad.getKey();
        if (key) {
          handleKeypadInput(key);
        }
        delay(10);
      }
      
      if (currentInput.length() > 0) {
        // Save user
        saveUser(uidStr, currentInput);
        
        tft.fillScreen(ILI9341_GREEN);
        tft.setCursor(20, 80);
        tft.setTextColor(ILI9341_BLACK);
        tft.print("USER REGISTERED");
        tft.setCursor(20, 110);
        tft.print(currentInput);
        
        playBeep(2000, 200);
      }
      
      inputMode = false;
      delay(2000);
      showRegistrationScreen();
      
    } else if (currentMode == MODE_ATTENDANCE) {
      // Attendance mode
      String userName = getUserName(uidStr);
      
      if (userName != "Unknown") {
        // Determine attendance type based on time
        struct tm timeinfo;
        getLocalTime(&timeinfo);
        
        String type = "MASUK";
        if (timeinfo.tm_hour >= istirahatMulai.jam && timeinfo.tm_hour < istirahatSelesai.jam) {
          type = "ISTIRAHAT";
        } else if (timeinfo.tm_hour >= jamPulang.jam) {
          type = "PULANG";
        }
        
        // Save attendance
        saveAttendance(uidStr, userName, type);
        
        tft.fillScreen(ILI9341_GREEN);
        tft.setCursor(10, 40);
        tft.setTextColor(ILI9341_BLACK);
        tft.setTextSize(2);
        tft.print("WELCOME");
        tft.setCursor(10, 70);
        tft.setTextSize(1);
        tft.print(userName);
        
        char timeStr[20];
        strftime(timeStr, sizeof(timeStr), "%H:%M:%S", &timeinfo);
        tft.setCursor(10, 100);
        tft.print(timeStr);
        
        tft.setCursor(10, 120);
        tft.print(type);
        
        playBeep(2000, 200);
      } else {
        tft.fillScreen(ILI9341_RED);
        tft.setCursor(10, 80);
        tft.setTextColor(ILI9341_WHITE);
        tft.print("UNREGISTERED CARD");
        tft.setCursor(10, 110);
        tft.print("Please register first");
        
        playBeep(500, 300);
      }
      
      delay(3000);
      showMainMenu();
    }
  }
  
  // Small delay to prevent overwhelming the loop
  delay(10);
}