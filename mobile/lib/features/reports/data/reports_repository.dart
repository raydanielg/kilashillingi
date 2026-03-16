import 'package:dio/dio.dart';

class ReportsRepository {
  ReportsRepository(this._dio);

  final Dio _dio;

  Future<Map<String, dynamic>> preview({required String type}) async {
    final res = await _dio.get(
      '/v1/reports/preview',
      queryParameters: {
        'type': type,
      },
    );
    return Map<String, dynamic>.from(res.data as Map);
  }
}
