import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'dart:convert';
import 'package:flutter_riverpod/flutter_riverpod.dart';

class TokenStorage {
  TokenStorage(this._storage);

  final FlutterSecureStorage _storage;

  static const _tokenKey = 'auth_token';
  static const _userKey = 'auth_user';
  static const _dashboardSummaryKey = 'dashboard_summary';

  Future<void> writeToken(String token) => _storage.write(key: _tokenKey, value: token);

  Future<String?> readToken() => _storage.read(key: _tokenKey);

  Future<void> deleteToken() => _storage.delete(key: _tokenKey);

  Future<Map<String, dynamic>?> readUser() async {
    final raw = await _storage.read(key: _userKey);
    if (raw == null || raw.isEmpty) return null;

    try {
      final decoded = jsonDecode(raw);
      if (decoded is Map) {
        return Map<String, dynamic>.from(decoded);
      }
    } catch (_) {}
    return null;
  }

  Future<void> writeUser(Map<String, dynamic> user) async {
    await _storage.write(key: _userKey, value: jsonEncode(user));
  }

  Future<void> deleteUser() => _storage.delete(key: _userKey);

  Future<Map<String, dynamic>?> readDashboardSummary() async {
    final raw = await _storage.read(key: _dashboardSummaryKey);
    if (raw == null || raw.isEmpty) return null;

    try {
      final decoded = jsonDecode(raw);
      if (decoded is Map) {
        return Map<String, dynamic>.from(decoded);
      }
    } catch (_) {}
    return null;
  }

  Future<void> writeDashboardSummary(Map<String, dynamic> summary) async {
    await _storage.write(key: _dashboardSummaryKey, value: jsonEncode(summary));
  }

  Future<void> deleteDashboardSummary() => _storage.delete(key: _dashboardSummaryKey);
}
