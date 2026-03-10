import 'package:dio/dio.dart';

class MetaRepository {
  MetaRepository(this._dio);

  final Dio _dio;

  Future<Map<String, dynamic>> currencies() async {
    final res = await _dio.get('/v1/meta/currencies');
    return Map<String, dynamic>.from(res.data as Map);
  }
}
