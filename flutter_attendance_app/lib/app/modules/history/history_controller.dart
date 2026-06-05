import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../data/services/api_service.dart';

class HistoryController extends GetxController {
  final ApiService _apiService = ApiService();

  final RxBool isLoading = false.obs;
  final RxList<Map<String, dynamic>> historyList = <Map<String, dynamic>>[].obs;
  final RxString selectedMonth = ''.obs;

  @override
  void onInit() {
    super.onInit();
    // Set to current month (YYYY-MM format)
    selectedMonth.value = DateFormat('yyyy-MM').format(DateTime.now());
    fetchHistory();
  }

  Future<void> fetchHistory() async {
    isLoading.value = true;
    
    try {
      final response = await _apiService.getHistory(month: selectedMonth.value);
      
      if (response['data'] != null) {
        historyList.value = List<Map<String, dynamic>>.from(
          response['data'].map((item) => Map<String, dynamic>.from(item)),
        );
      } else {
        historyList.clear();
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

  Future<void> selectMonth(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime.now(),
      initialDatePickerMode: DatePickerMode.day,
      helpText: 'Pilih Bulan',
    );

    if (picked != null) {
      selectedMonth.value = DateFormat('yyyy-MM').format(picked);
      await fetchHistory();
    }
  }

  String formatDate(String? dateString) {
    if (dateString == null || dateString.isEmpty) return '-';
    
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMM yyyy', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  String getStatusLabel(String? status) {
    switch (status) {
      case 'present':
        return 'Hadir';
      case 'late':
        return 'Terlambat';
      case 'absent':
        return 'Tidak Hadir';
      case 'leave':
        return 'Izin';
      case 'sick':
        return 'Sakit';
      default:
        return status ?? 'Unknown';
    }
  }

  Color getStatusColor(String? status) {
    switch (status) {
      case 'present':
        return Colors.green;
      case 'late':
        return Colors.orange;
      case 'absent':
        return Colors.red;
      case 'leave':
        return Colors.blue;
      case 'sick':
        return Colors.purple;
      default:
        return Colors.grey;
    }
  }
}
