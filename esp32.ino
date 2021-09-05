#include <WiFi.h> // Library untuk Menghubungkan Mikrokontroller dengan WiFi
#include <HTTPClient.h> // Library untuk Memulai Koneksi Web Service

#define pinLDR 32 // Menggunakan Pin 32 untuk Sensor LDR
#define pinLED 2 // Menggunakan Pin 2 untuk LED

#define led_on HIGH // Mendefinisikan led_on untuk Logika High
#define led_off LOW // Mendefinisikan led_off untuk Logika LOW

const char* ssid = "75d0d7_plus"; // SSID WiFi yang digunakan
const char* password = "279335822"; // Password WiFi yang digunakan

const char* postURL = "http://192.168.0.12:5010/esp_sensor"; // URL API POST untuk Memasukkan Hasil Pembacaan Sensor LDR ke Database
const char* getURL = "http://192.168.0.12:5010/esp_command"; // URL API GET untuk Mendapatkan Perintah LED (On/Off)

void setup() {
  Serial.begin(115200);
  pinMode(pinLED, OUTPUT);
  digitalWrite(pinLED, led_off);

  WiFi.begin(ssid, password); // Menghubungkan ke WiFi
  Serial.println("Connecting");
  while (WiFi.status() != WL_CONNECTED) {
    delay(250);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
  Serial.println("");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    delay(500);
    getCommand(); // Memanggil fungsi getCommand untuk Memanggil API GET
    delay(1000);
    postSensor(); // Memanggil fungsi postSensor untuk Memanggil API POST
  }
  else {
    Serial.println("Tidak dapat terkoneksi ke jaringan WiFi, Harap Restart Mikrokontroller yang Digunakan!!");
  }
  delay(1000);
}

void postSensor() {
  String nilaiLDR = String(analogRead(pinLDR)); // Variabel Hasil Pembacaan Sensor LDR
  // Memulai Koneksi API POST untuk Memasukkan Nilai Hasil Pembacaan Sensor LDR ke Database
  HTTPClient http;
  http.begin(postURL); 
  http.addHeader("Content-Type", "application/json");
  String payload = "{\n\t\"nilai\":\"" + nilaiLDR + "\"\n}";
  Serial.print("Data yang akan dikirim: ");
  Serial.println(payload);
  int httpResponseCode = http.POST(payload);
  Serial.print("Post Code: ");
  Serial.println(httpResponseCode);
  // Mengakhiri Koneksi API POST
  http.end();
  Serial.println("");
}

void getCommand() {
  Serial.println("Memulai mengambil data dari database dengan HTTP GET");
  // Memulai Koneksi API GET untuk Mendapatkan Perintah LED (On/Off)
  HTTPClient http;
  http.begin(getURL);
  int httpResponseCode = http.GET();
  if (httpResponseCode > 0) {
    Serial.printf("[HTTP] Get code: %d\n", httpResponseCode);
    if (httpResponseCode == HTTP_CODE_OK || httpResponseCode == HTTP_CODE_MOVED_PERMANENTLY) {
      String dataGet = http.getString();
      Serial.print("Data pada Database : ");
      Serial.println(dataGet);
      // Menyalakan LED Jika Tombol di Web diTekan
      if (dataGet.indexOf("on") > 0) {
        digitalWrite(pinLED, led_on);
        Serial.println("Lampu Nyala");
      }
      // Mematikan LED Jika Tombol di Web diTekan
      else if (dataGet.indexOf("off") > 0) {
        digitalWrite(pinLED, led_off);
        Serial.println("Lampu Mati");
      }
    } else {
      Serial.println("Gagal Mengambil Data");
    }
  } else {
    // Menampilkan Error 
    Serial.printf("[HTTP] GET failed, error: %s\n", http.errorToString(httpResponseCode).c_str());
  }
  // Mengakhiri Koneksi API GET
  http.end();
  Serial.println("");
}
