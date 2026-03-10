import 'package:dio/dio.dart';

class DashboardRepository {
  DashboardRepository(this._dio);

  final Dio _dio;

  Future<Map<String, dynamic>> summary() async {
    final res = await _dio.get('/v1/dashboard/summary');
    return Map<String, dynamic>.from(res.data as Map);
  }
}
