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
const char* serverURL = "https://poshan.my.id/hm";
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
const unsigned long TAP_DISPLAY_DURATION = 5000; // Tampilkan 5 detik

// Watchdog timer
unsigned long lastWatchdogFeed = 0;
const unsigned long WATCHDOG_FEED_INTERVAL = 1000; // Feed watchdog setiap 1 detik

// Memory monitoring
unsigned long lastMemoryCheck = 0;
const unsigned long MEMORY_CHECK_INTERVAL = 10000; // Cek memory setiap 10 detik
size_t minFreeHeap = 0xFFFFFFFF;

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
const unsigned long RFID_CHECK_INTERVAL = 200;
unsigned long lastDisplayUpdate = 0;
const unsigned long DISPLAY_UPDATE_INTERVAL = 300;
unsigned long lastBlinkTime = 0;
bool blinkState = false;

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
bool sendDataToServer(String uid, String name, String type, bool isOfflineRetry = false);
void saveToOfflineQueue(String uid, String name, String type);
void processOfflineQueue();
void sendNextOfflineData();
void checkModeFromServer();
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
  
  const int bufferSize = 64; // Reduce buffer size
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
      buffer[i] = 4000 * sin(2 * PI * freq * t);
    }

    size_t bytes_written;
    i2s_write(I2S_PORT, buffer, bufferSize * sizeof(int16_t),
              &bytes_written, 100); // Timeout 100ms

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
  
  yield(); // Feed watchdog
}

void playPreviewBeep() {
  playBeep(1200, 20);
}

/* =========================
   DISPLAY FUNCTIONS
========================= */

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
  tft.fillScreen(ILI9341_BLUE);
  tft.setTextColor(ILI9341_WHITE);
  tft.setTextSize(2);
  tft.setCursor(30, 20);
  tft.print("ATTENDANCE MODE");
  
  tft.setTextSize(1);
  tft.setCursor(20, 60);
  tft.print("Tap your card...");
  tft.setCursor(20, 80);
  tft.print("Server Mode: " + serverMode);
  tft.setCursor(20, 100);
  tft.print("Press # to menu");
  
  // Tampilkan data tap terakhir jika ada
  if (lastEmployeeName.length() > 0 && millis() - lastTapDisplay < TAP_DISPLAY_DURATION) {
    tft.fillRect(10, 130, 300, 80, ILI9341_GREEN);
    tft.drawRect(10, 130, 300, 80, ILI9341_WHITE);
    
    tft.setTextColor(ILI9341_BLACK);
    tft.setTextSize(2);
    tft.setCursor(20, 140);
    tft.print("BERHASIL!");
    
    tft.setTextSize(1);
    tft.setCursor(20, 165);
    tft.print("Nama: " + lastEmployeeName);
    
    tft.setCursor(20, 185);
    tft.print("Waktu: " + lastTapTime);
  } else if (lastEmployeeName.length() > 0 && millis() - lastTapDisplay >= TAP_DISPLAY_DURATION) {
    // Clear data setelah durasi habis
    lastEmployeeName = "";
    lastTapTime = "";
  }
  
  if (offlineQueue.size() > 0) {
    tft.setTextColor(ILI9341_YELLOW);
    tft.setCursor(20, 220);
    tft.print("Offline: " + String(offlineQueue.size()));
  }
  
  if (statusMessage.length() > 0 && lastEmployeeName.length() == 0) {
    tft.setTextSize(2);
    tft.setTextColor(ILI9341_YELLOW);
    tft.setCursor(20, 160);
    tft.print(statusMessage);
  }
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
  char key = 0;
  
  for (int i = 0; i < 3; i++) {
    key = keypad.getKey();
    if (key != 0) break;
    delay(1);
  }
  
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
  
  Serial.print("Key: ");
  Serial.print(key);
  Serial.print(" Mode:");
  Serial.print(currentMode);
  Serial.print(" InputMode:");
  Serial.print(inputMode);
  Serial.print(" Step:");
  Serial.println(alarmSettingStep);
  
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
      displayNeedsUpdate = true;
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

/* =========================
   SERVER MODE FUNCTIONS
========================= */

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
        } else if (serverMode == "register" && currentMode != MODE_REGISTER) {
          currentMode = MODE_REGISTER;
          Serial.println("✅ Switching to REGISTER MODE");
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
    DynamicJsonDocument responseDoc(1024);
    DeserializationError error = deserializeJson(responseDoc, response);
    
    if (!error && responseDoc["success"] == true) {
      // Ambil data karyawan dari response
      if (responseDoc.containsKey("employee")) {
        lastEmployeeName = responseDoc["employee"]["name"].as<String>();
      }
      
      if (responseDoc.containsKey("time")) {
        lastTapTime = responseDoc["time"].as<String>();
      }
      
      if (responseDoc.containsKey("type")) {
        String tapType = responseDoc["type"].as<String>();
        lastTapTime = tapType + " " + lastTapTime;
      }
      
      lastTapDisplay = millis();
      success = true;
    }
    
    // Cleanup
    response = String();
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
  // Feed ESP32 watchdog timer
  yield();
  
  // Update timestamp
  lastWatchdogFeed = millis();
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
  
  uint8_t uid[7];
  uint8_t uidLength;
  
  if (nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength, 50)) {
    if (millis() - lastCardRead < CARD_READ_COOLDOWN) return;
    lastCardRead = millis();
    
    String uidStr = "";
    uidStr.reserve(20);
    
    for (uint8_t i = 0; i < uidLength; i++) {
      if (uid[i] < 0x10) uidStr += "0";
      uidStr += String(uid[i], HEX);
    }
    uidStr.toUpperCase();
    
    Serial.print("RFID: ");
    Serial.println(uidStr);
    
    // Beep awal
    playBeep(1500, 50);
    yield();
    
    // Gunakan mode dari server untuk menentukan aksi
    if (serverMode == "attendance") {
      statusMessage = "Sending...";
      displayNeedsUpdate = true;
      updateDisplay(true); // Force update
      
      yield(); // Feed watchdog sebelum HTTP
      
      bool sendSuccess = sendDataToServer(uidStr, "", "attendance", false);
      
      yield(); // Feed watchdog setelah HTTP
      
      if (sendSuccess) {
        statusMessage = "Recorded!";
        
        // Beep sukses
        playBeep(2000, 100);
        yield();
        
        // Text-to-speech pattern
        playBeep(1800, 80);
        yield();
        playBeep(2200, 80);
        yield();
        
        Serial.println("✅ Attendance recorded");
        displayNeedsUpdate = true;
      } else {
        saveToOfflineQueue(uidStr, "", "attendance");
        statusMessage = "Offline saved";
        playBeep(1000, 100);
        yield();
      }
      
      statusMessageTime = millis();
      displayNeedsUpdate = true;
      
    } else if (serverMode == "register") {
      tempUID = uidStr;
      
      // Kirim ke server untuk cache
      if (WiFi.status() == WL_CONNECTED) {
        yield();
        
        InsecureWiFiClient client;
        client.setTimeout(2000); // Timeout pendek
        HTTPClient http;
        
        char url[128];
        snprintf(url, sizeof(url), "%s%s/card-detected", serverURL, apiEndpoint.c_str());
        
        http.begin(client, url);
        http.addHeader("Content-Type", "application/json");
        http.setTimeout(2000);
        
        DynamicJsonDocument doc(256);
        doc["uid"] = uidStr;
        doc["mode"] = "register";
        
        String jsonString;
        serializeJson(doc, jsonString);
        
        http.POST(jsonString);
        http.end();
        client.stop();
        
        jsonString = String();
        
        yield();
      }
      
      inputMode = true;
      currentInput = "";
      lastKey = 0;
      previewChar = "";
      statusMessage = "Enter name";
      statusMessageTime = millis();
      displayNeedsUpdate = true;
      
      // Beep untuk register
      playBeep(1500, 100);
      yield();
      playBeep(1500, 100);
      yield();
      
      Serial.println("✅ Card detected for registration");
    }
    
    // Cleanup
    uidStr = String();
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
  
  Serial.println("\n=== STARTING ===");
  
  Wire.begin(SDA_PIN, SCL_PIN, 50000);
  Serial.println("I2C OK");
  
  tft.begin();
  tft.setRotation(1);
  tft.fillScreen(ILI9341_BLACK);
  tft.setTextColor(ILI9341_GREEN);
  tft.setCursor(40, 100);
  tft.print("SYSTEM READY");
  Serial.println("TFT OK");
  
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
  
  preferences.begin("attendance", false);
  
  ssid = preferences.getString("ssid", "");
  password = preferences.getString("password", "");
  loadAlarmSettings();
  
  // Load offline queue count
  int offlineCount = preferences.getInt("offline_count", 0);
  if (offlineCount > 0) {
    Serial.printf("📂 Ada %d data offline tersimpan\n", offlineCount);
  }
  
  if (ssid.length() > 0) {
    WiFi.begin(ssid.c_str(), password.c_str());
  }
  
  delay(1000);
  showMainMenu();
  playBeep(1500, 100);
  
  Serial.println("=== READY ===\n");
}

/* =========================
   LOOP
========================= */

void loop() {
  // Feed watchdog secara berkala
  if (millis() - lastWatchdogFeed > WATCHDOG_FEED_INTERVAL) {
    feedWatchdog();
  }
  
  // Check memory secara berkala
  if (millis() - lastMemoryCheck > MEMORY_CHECK_INTERVAL) {
    checkMemory();
    lastMemoryCheck = millis();
  }
  
  // Cek mode dari server (PRIORITAS TINGGI untuk responsivitas)
  if (WiFi.status() == WL_CONNECTED) {
    if (millis() - lastModeCheck > MODE_CHECK_INTERVAL) {
      checkModeFromServer();
      lastModeCheck = millis();
    }
  }
  
  checkKeypad();
  checkRFID();
  
  // Proses offline queue (non-blocking)
  processOfflineQueue();
  
  // Update display
  if (millis() - lastDisplayUpdate > DISPLAY_UPDATE_INTERVAL) {
    lastDisplayUpdate = millis();
    updateDisplay(false);
  }
  
  // Auto-clear status message
  if (statusMessage.length() > 0 && millis() - statusMessageTime > 3000) {
    statusMessage = "";
    displayNeedsUpdate = true;
  }
  
  // Cleanup strings periodically
  cleanupStrings();
  
  if (errorCount > 10) {
    resetKeypad();
    errorCount = 0;
  }
  
  // Minimal delay untuk yield
  yield();
}