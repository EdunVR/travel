import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../../data/services/api_service.dart';
import '../../data/services/location_service.dart';
import '../../routes/app_routes.dart';

class HomeController extends GetxController {
  final ApiService _apiService = ApiService();
  final LocationService _locationService = LocationService();
  final ImagePicker _imagePicker = ImagePicker();

  final RxBool isLoading = false.obs;
  final RxBool isClockingIn = false.obs;
  final RxBool isClockingOut = false.obs;

  final RxString userName = ''.obs;
  final RxString currentDate = ''.obs;
  final RxString currentTime = ''.obs;

  // Today's attendance status
  final RxMap<String, dynamic> todayStatus = <String, dynamic>{}.obs;
  final RxBool hasClockedIn = false.obs;
  final RxBool hasClockedOut = false.obs;
  final RxString clockInTime = ''.obs;
  final RxString clockOutTime = ''.obs;
  final RxString workDuration = ''.obs;

  // Selfie preview paths (for local display after capture)
  final Rx<File?> selfieInFile = Rx<File?>(null);
  final Rx<File?> selfieOutFile = Rx<File?>(null);

  @override
  void onInit() {
    super.onInit();
    loadUserInfo();
    updateDateTime();
    fetchTodayStatus();

    Stream.periodic(const Duration(seconds: 1)).listen((_) {
      updateDateTime();
    });
  }

  Future<void> loadUserInfo() async {
    final prefs = await SharedPreferences.getInstance();
    userName.value = prefs.getString('user_name') ?? 'User';
  }

  void updateDateTime() {
    final now = DateTime.now();
    final days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    final months = [
      '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    final dayName = days[now.weekday % 7];
    final monthName = months[now.month];
    currentDate.value =
        '$dayName, ${now.day.toString().padLeft(2, '0')} $monthName ${now.year}';
    currentTime.value =
        '${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}:${now.second.toString().padLeft(2, '0')}';
  }

  Future<void> fetchTodayStatus() async {
    isLoading.value = true;
    try {
      final response = await _apiService.getTodayStatus();
      if (response['data'] != null) {
        todayStatus.value = response['data'];
        hasClockedIn.value = todayStatus['clock_in'] != null;
        hasClockedOut.value = todayStatus['clock_out'] != null;
        clockInTime.value = todayStatus['clock_in'] ?? '';
        clockOutTime.value = todayStatus['clock_out'] ?? '';
        workDuration.value = todayStatus['work_duration'] ?? '';
      } else {
        hasClockedIn.value = false;
        hasClockedOut.value = false;
        clockInTime.value = '';
        clockOutTime.value = '';
        workDuration.value = '';
      }
    } catch (e) {
      Get.snackbar(
        'Error', e.toString().replaceAll('Exception: ', ''),
        backgroundColor: Colors.red[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
      );
    } finally {
      isLoading.value = false;
    }
  }

  // ── Ambil foto selfie dari kamera depan ─────────────────────────────────
  Future<String?> _takeSelfie() async {
    try {
      final XFile? photo = await _imagePicker.pickImage(
        source: ImageSource.camera,
        preferredCameraDevice: CameraDevice.front,
        imageQuality: 70,     // kompres agar tidak terlalu besar
        maxWidth: 800,
        maxHeight: 800,
      );
      if (photo == null) return null;
      final bytes = await File(photo.path).readAsBytes();
      return base64Encode(bytes);
    } catch (e) {
      Get.snackbar(
        'Error Kamera',
        'Gagal mengambil foto: ${e.toString().replaceAll('Exception: ', '')}',
        backgroundColor: Colors.red[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
      );
      return null;
    }
  }

  // ── Clock In ────────────────────────────────────────────────────────────
  Future<void> clockIn() async {
    isClockingIn.value = true;
    try {
      // 1. Ambil foto selfie
      final selfieBase64 = await _takeSelfie();
      if (selfieBase64 == null) {
        // User membatalkan kamera
        return;
      }

      // 2. Izin lokasi
      bool hasPermission = await _locationService.requestLocationPermission();
      if (!hasPermission) {
        _showLocationPermissionDialog();
        return;
      }

      // 3. Dapatkan posisi
      Position position = await _locationService.getCurrentPosition();
      String address = await _locationService.getAddressFromCoordinates(
          position.latitude, position.longitude);
      String deviceInfo = _locationService.getDeviceInfo();

      // 4. Kirim ke API
      final response = await _apiService.clockIn(
        latitude: position.latitude,
        longitude: position.longitude,
        address: address,
        deviceInfo: deviceInfo,
        selfieBase64: selfieBase64,
      );

      Get.snackbar(
        'Berhasil',
        response['message'] ?? 'Clock in berhasil',
        backgroundColor: Colors.green[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
        icon: const Icon(Icons.check_circle, color: Colors.white),
      );

      await fetchTodayStatus();
    } catch (e) {
      Get.snackbar(
        'Error', e.toString().replaceAll('Exception: ', ''),
        backgroundColor: Colors.red[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
        duration: const Duration(seconds: 4),
      );
    } finally {
      isClockingIn.value = false;
    }
  }

  // ── Clock Out ───────────────────────────────────────────────────────────
  Future<void> clockOut() async {
    isClockingOut.value = true;
    try {
      // 1. Ambil foto selfie
      final selfieBase64 = await _takeSelfie();
      if (selfieBase64 == null) {
        return;
      }

      // 2. Izin lokasi
      bool hasPermission = await _locationService.requestLocationPermission();
      if (!hasPermission) {
        _showLocationPermissionDialog();
        return;
      }

      // 3. Dapatkan posisi
      Position position = await _locationService.getCurrentPosition();
      String address = await _locationService.getAddressFromCoordinates(
          position.latitude, position.longitude);
      String deviceInfo = _locationService.getDeviceInfo();

      // 4. Kirim ke API
      final response = await _apiService.clockOut(
        latitude: position.latitude,
        longitude: position.longitude,
        address: address,
        deviceInfo: deviceInfo,
        selfieBase64: selfieBase64,
      );

      Get.snackbar(
        'Berhasil',
        response['message'] ?? 'Clock out berhasil',
        backgroundColor: Colors.green[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
        icon: const Icon(Icons.check_circle, color: Colors.white),
      );

      await fetchTodayStatus();
    } catch (e) {
      Get.snackbar(
        'Error', e.toString().replaceAll('Exception: ', ''),
        backgroundColor: Colors.red[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
        duration: const Duration(seconds: 4),
      );
    } finally {
      isClockingOut.value = false;
    }
  }

  void _showLocationPermissionDialog() {
    Get.defaultDialog(
      title: 'Izin Lokasi',
      middleText:
          'Aplikasi memerlukan izin lokasi untuk absensi. Silakan aktifkan di pengaturan.',
      confirm: ElevatedButton(
        onPressed: () {
          Get.back();
          _locationService.openLocationSettings();
        },
        child: const Text('Buka Pengaturan'),
      ),
      cancel: TextButton(
        onPressed: () => Get.back(),
        child: const Text('Batal'),
      ),
    );
  }

  Future<void> logout() async {
    Get.defaultDialog(
      title: 'Logout',
      middleText: 'Apakah Anda yakin ingin keluar?',
      confirm: ElevatedButton(
        onPressed: () async {
          await _apiService.logout();
          Get.offAllNamed(AppRoutes.LOGIN);
        },
        style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
        child: const Text('Ya, Keluar'),
      ),
      cancel: TextButton(
        onPressed: () => Get.back(),
        child: const Text('Batal'),
      ),
    );
  }

  void goToHistory() {
    Get.toNamed(AppRoutes.HISTORY);
  }
}
