import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:geolocator/geolocator.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../../data/services/api_service.dart';
import '../../data/services/location_service.dart';
import '../../routes/app_routes.dart';

class HomeController extends GetxController {
  final ApiService _apiService = ApiService();
  final LocationService _locationService = LocationService();

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

  @override
  void onInit() {
    super.onInit();
    loadUserInfo();
    updateDateTime();
    fetchTodayStatus();
    
    // Update time every second
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
    // Format tanggal: Senin, 05 Juni 2026
    final days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    final months = [
      '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    final dayName = days[now.weekday % 7];
    final monthName = months[now.month];
    currentDate.value = '$dayName, ${now.day.toString().padLeft(2, '0')} $monthName ${now.year}';
    currentTime.value = '${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}:${now.second.toString().padLeft(2, '0')}';
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
        // No attendance today
        hasClockedIn.value = false;
        hasClockedOut.value = false;
        clockInTime.value = '';
        clockOutTime.value = '';
        workDuration.value = '';
      }
    } catch (e) {
      Get.snackbar(
        'Error',
        e.toString().replaceAll('Exception: ', ''),
        backgroundColor: Colors.red[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
      );
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> clockIn() async {
    isClockingIn.value = true;
    
    try {
      // Request location permission
      bool hasPermission = await _locationService.requestLocationPermission();
      
      if (!hasPermission) {
        Get.defaultDialog(
          title: 'Izin Lokasi',
          middleText: 'Aplikasi memerlukan izin lokasi untuk absensi. Silakan aktifkan di pengaturan.',
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
        return;
      }

      // Get current position
      Position position = await _locationService.getCurrentPosition();
      
      // Get address
      String address = await _locationService.getAddressFromCoordinates(
        position.latitude,
        position.longitude,
      );
      
      // Get device info
      String deviceInfo = _locationService.getDeviceInfo();
      
      // Call API
      final response = await _apiService.clockIn(
        latitude: position.latitude,
        longitude: position.longitude,
        address: address,
        deviceInfo: deviceInfo,
      );
      
      Get.snackbar(
        'Berhasil',
        response['message'] ?? 'Clock in berhasil',
        backgroundColor: Colors.green[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
        icon: const Icon(Icons.check_circle, color: Colors.white),
      );
      
      // Refresh status
      await fetchTodayStatus();
      
    } catch (e) {
      Get.snackbar(
        'Error',
        e.toString().replaceAll('Exception: ', ''),
        backgroundColor: Colors.red[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
        duration: const Duration(seconds: 4),
      );
    } finally {
      isClockingIn.value = false;
    }
  }

  Future<void> clockOut() async {
    isClockingOut.value = true;
    
    try {
      // Request location permission
      bool hasPermission = await _locationService.requestLocationPermission();
      
      if (!hasPermission) {
        Get.defaultDialog(
          title: 'Izin Lokasi',
          middleText: 'Aplikasi memerlukan izin lokasi untuk absensi. Silakan aktifkan di pengaturan.',
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
        return;
      }

      // Get current position
      Position position = await _locationService.getCurrentPosition();
      
      // Get address
      String address = await _locationService.getAddressFromCoordinates(
        position.latitude,
        position.longitude,
      );
      
      // Get device info
      String deviceInfo = _locationService.getDeviceInfo();
      
      // Call API
      final response = await _apiService.clockOut(
        latitude: position.latitude,
        longitude: position.longitude,
        address: address,
        deviceInfo: deviceInfo,
      );
      
      Get.snackbar(
        'Berhasil',
        response['message'] ?? 'Clock out berhasil',
        backgroundColor: Colors.green[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
        icon: const Icon(Icons.check_circle, color: Colors.white),
      );
      
      // Refresh status
      await fetchTodayStatus();
      
    } catch (e) {
      Get.snackbar(
        'Error',
        e.toString().replaceAll('Exception: ', ''),
        backgroundColor: Colors.red[400],
        colorText: Colors.white,
        snackPosition: SnackPosition.TOP,
        duration: const Duration(seconds: 4),
      );
    } finally {
      isClockingOut.value = false;
    }
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
