import 'package:dio/dio.dart';

class AuthRepository {
  AuthRepository(this._dio);

  final Dio _dio;

  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    final res = await _dio.post(
      '/v1/auth/login',
      data: {
        'email': email,
        'password': password,
      },
    );
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String phone,
    required String currency,
    required String password,
    required String passwordConfirmation,
  }) async {
    final res = await _dio.post(
      '/v1/auth/register',
      data: {
        'name': name,
        'email': email,
        'phone': phone,
        'currency': currency,
        'password': password,
        'password_confirmation': passwordConfirmation,
      },
    );
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<Map<String, dynamic>> me() async {
    final res = await _dio.get('/v1/auth/me');
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<void> logout() async {
    await _dio.post('/v1/auth/logout');
  }
}
