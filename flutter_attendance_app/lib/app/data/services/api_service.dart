import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  // TODO: Replace with your actual Laravel API base URL
  static const String baseUrl = 'https://hmtourtravel.com/api/mobile/v1';
  
  // Authentication Headers
  Future<Map<String, String>> _getHeaders() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token') ?? '';
    
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token.isNotEmpty) 'Authorization': 'Bearer $token',
    };
  }

  // Login
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: await _getHeaders(),
        body: json.encode({
          'email': email,
          'password': password,
        }),
      );

      final data = json.decode(response.body);
      
      if (response.statusCode == 200 && data['success'] == true) {
        // Save token
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', data['data']['token']);
        await prefs.setString('user_id', data['data']['user_id'].toString());
        await prefs.setString('employee_id', data['data']['employee_id'].toString());
        await prefs.setString('user_name', data['data']['name']);
        await prefs.setString('user_email', data['data']['email']);
        
        return data;
      } else {
        throw Exception(data['message'] ?? 'Login failed');
      }
    } catch (e) {
      throw Exception('Network error: ${e.toString()}');
    }
  }

  // Logout
  Future<void> logout() async {
    try {
      await http.post(
        Uri.parse('$baseUrl/logout'),
        headers: await _getHeaders(),
      );
    } catch (e) {
      // Silent fail - still clear local data
    }
    
    // Clear local data
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
  }

  // Get Today's Status
  Future<Map<String, dynamic>> getTodayStatus() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/attendance/today'),
        headers: await _getHeaders(),
      );

      final data = json.decode(response.body);
      
      if (response.statusCode == 200 && data['success'] == true) {
        return data;
      } else {
        throw Exception(data['message'] ?? 'Failed to fetch status');
      }
    } catch (e) {
      throw Exception('Network error: ${e.toString()}');
    }
  }

  // Clock In
  Future<Map<String, dynamic>> clockIn({
    required double latitude,
    required double longitude,
    String? address,
    String? deviceInfo,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/attendance/clock-in'),
        headers: await _getHeaders(),
        body: json.encode({
          'latitude': latitude,
          'longitude': longitude,
          'address': address,
          'device_info': deviceInfo,
        }),
      );

      final data = json.decode(response.body);
      
      if (response.statusCode == 200 && data['success'] == true) {
        return data;
      } else {
        throw Exception(data['message'] ?? 'Clock in failed');
      }
    } catch (e) {
      throw Exception('Network error: ${e.toString()}');
    }
  }

  // Clock Out
  Future<Map<String, dynamic>> clockOut({
    required double latitude,
    required double longitude,
    String? address,
    String? deviceInfo,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/attendance/clock-out'),
        headers: await _getHeaders(),
        body: json.encode({
          'latitude': latitude,
          'longitude': longitude,
          'address': address,
          'device_info': deviceInfo,
        }),
      );

      final data = json.decode(response.body);
      
      if (response.statusCode == 200 && data['success'] == true) {
        return data;
      } else {
        throw Exception(data['message'] ?? 'Clock out failed');
      }
    } catch (e) {
      throw Exception('Network error: ${e.toString()}');
    }
  }

  // Get History
  Future<Map<String, dynamic>> getHistory({required String month}) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/attendance/history?month=$month'),
        headers: await _getHeaders(),
      );

      final data = json.decode(response.body);
      
      if (response.statusCode == 200 && data['success'] == true) {
        return data;
      } else {
        throw Exception(data['message'] ?? 'Failed to fetch history');
      }
    } catch (e) {
      throw Exception('Network error: ${e.toString()}');
    }
  }
}
