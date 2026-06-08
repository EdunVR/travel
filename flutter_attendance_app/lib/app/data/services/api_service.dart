import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  /// Base URL ke backend Laravel di Hostinger
  /// Sesuai URL_HOSTINGER di .env: https://hmtourtravel.com
  static const String baseUrl = 'https://hmtourtravel.com/api/mobile/v1';

  /// Timeout per request
  static const Duration _timeout = Duration(seconds: 30);

  // ── Headers ───────────────────────────────────────────────────────────────
  Future<Map<String, String>> _getHeaders() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token') ?? '';

    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token.isNotEmpty) 'Authorization': 'Bearer $token',
    };
  }

  // ── Helper: decode response & throw readable error ────────────────────────
  Map<String, dynamic> _decode(http.Response response) {
    try {
      return json.decode(response.body) as Map<String, dynamic>;
    } catch (_) {
      throw Exception('Server mengembalikan respons yang tidak valid (HTTP ${response.statusCode})');
    }
  }

  String _friendlyError(dynamic e) {
    if (e is SocketException) return 'Tidak ada koneksi internet. Cek WiFi/data Anda.';
    if (e is TimeoutException) return 'Server lambat merespons. Coba lagi.';
    if (e is HandshakeException) return 'Koneksi SSL gagal. Pastikan waktu device sudah benar.';
    final msg = e.toString().replaceAll('Exception: ', '');
    return msg.startsWith('Network error:') ? msg : msg;
  }

  // ── Login ─────────────────────────────────────────────────────────────────
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/login'),
            headers: await _getHeaders(),
            body: json.encode({'email': email, 'password': password}),
          )
          .timeout(_timeout);

      final data = _decode(response);

      if (response.statusCode == 200 && data['success'] == true) {
        final prefs = await SharedPreferences.getInstance();
        final d = data['data'];
        await prefs.setString('auth_token',   d['token'].toString());
        await prefs.setString('user_id',      d['user_id'].toString());
        await prefs.setString('employee_id',  d['employee_id'].toString());
        await prefs.setString('user_name',    d['name'].toString());
        await prefs.setString('user_email',   d['email'].toString());
        return data;
      }

      throw Exception(data['message'] ?? 'Login gagal');
    } catch (e) {
      if (e is Exception && e.toString().contains('Login')) rethrow;
      throw Exception(_friendlyError(e));
    }
  }

  // ── Logout ────────────────────────────────────────────────────────────────
  Future<void> logout() async {
    try {
      await http
          .post(Uri.parse('$baseUrl/logout'), headers: await _getHeaders())
          .timeout(_timeout);
    } catch (_) {
      // Silent fail — tetap hapus token lokal
    }
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
  }

  // ── Today Status ──────────────────────────────────────────────────────────
  Future<Map<String, dynamic>> getTodayStatus() async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/attendance/today'), headers: await _getHeaders())
          .timeout(_timeout);

      final data = _decode(response);

      if (response.statusCode == 200 && data['success'] == true) return data;
      if (response.statusCode == 401) throw Exception('Sesi habis. Silakan login kembali.');
      throw Exception(data['message'] ?? 'Gagal mengambil status absensi');
    } catch (e) {
      if (e is Exception && !e.toString().contains('Network')) rethrow;
      throw Exception(_friendlyError(e));
    }
  }

  // ── Clock In ──────────────────────────────────────────────────────────────
  Future<Map<String, dynamic>> clockIn({
    required double latitude,
    required double longitude,
    String? address,
    String? deviceInfo,
    String? selfieBase64,
  }) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/attendance/clock-in'),
            headers: await _getHeaders(),
            body: json.encode({
              'latitude':    latitude,
              'longitude':   longitude,
              'address':     address,
              'device_info': deviceInfo,
              if (selfieBase64 != null) 'selfie_in': selfieBase64,
            }),
          )
          .timeout(_timeout);

      final data = _decode(response);

      if (response.statusCode == 200 && data['success'] == true) return data;
      if (response.statusCode == 401) throw Exception('Sesi habis. Silakan login kembali.');
      if (response.statusCode == 409) throw Exception(data['message'] ?? 'Sudah absen masuk hari ini.');
      throw Exception(data['message'] ?? 'Absen masuk gagal');
    } catch (e) {
      if (e is Exception && !e.toString().contains('Network')) rethrow;
      throw Exception(_friendlyError(e));
    }
  }

  // ── Clock Out ─────────────────────────────────────────────────────────────
  Future<Map<String, dynamic>> clockOut({
    required double latitude,
    required double longitude,
    String? address,
    String? deviceInfo,
    String? selfieBase64,
  }) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/attendance/clock-out'),
            headers: await _getHeaders(),
            body: json.encode({
              'latitude':    latitude,
              'longitude':   longitude,
              'address':     address,
              'device_info': deviceInfo,
              if (selfieBase64 != null) 'selfie_out': selfieBase64,
            }),
          )
          .timeout(_timeout);

      final data = _decode(response);

      if (response.statusCode == 200 && data['success'] == true) return data;
      if (response.statusCode == 401) throw Exception('Sesi habis. Silakan login kembali.');
      if (response.statusCode == 400) throw Exception(data['message'] ?? 'Belum absen masuk hari ini.');
      if (response.statusCode == 409) throw Exception(data['message'] ?? 'Sudah absen keluar hari ini.');
      throw Exception(data['message'] ?? 'Absen keluar gagal');
    } catch (e) {
      if (e is Exception && !e.toString().contains('Network')) rethrow;
      throw Exception(_friendlyError(e));
    }
  }

  // ── History ───────────────────────────────────────────────────────────────
  Future<Map<String, dynamic>> getHistory({required String month}) async {
    try {
      final response = await http
          .get(
            Uri.parse('$baseUrl/attendance/history?month=$month'),
            headers: await _getHeaders(),
          )
          .timeout(_timeout);

      final data = _decode(response);

      if (response.statusCode == 200 && data['success'] == true) return data;
      if (response.statusCode == 401) throw Exception('Sesi habis. Silakan login kembali.');
      throw Exception(data['message'] ?? 'Gagal mengambil riwayat absensi');
    } catch (e) {
      if (e is Exception && !e.toString().contains('Network')) rethrow;
      throw Exception(_friendlyError(e));
    }
  }

  // ── Check token validity ──────────────────────────────────────────────────
  Future<bool> isTokenValid() async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/attendance/today'), headers: await _getHeaders())
          .timeout(const Duration(seconds: 10));
      return response.statusCode != 401;
    } catch (_) {
      return false;
    }
  }
}
