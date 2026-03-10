import 'package:dio/dio.dart';

class ProfileRepository {
  ProfileRepository(this._dio);

  final Dio _dio;

  Future<Map<String, dynamic>> getProfile() async {
    final res = await _dio.get('/v1/profile');
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<Map<String, dynamic>> updateProfile({
    required String name,
    required String email,
    required String phone,
    required String currency,
  }) async {
    final res = await _dio.put(
      '/v1/profile',
      data: {
        'name': name,
        'email': email,
        'phone': phone,
        'currency': currency,
      },
    );
    return Map<String, dynamic>.from(res.data as Map);
  }
}
