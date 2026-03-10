import 'package:dio/dio.dart';

class BudgetsRepository {
  BudgetsRepository(this._dio);

  final Dio _dio;

  Future<Map<String, dynamic>> current() async {
    final res = await _dio.get('/v1/budgets/current');
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<Map<String, dynamic>> upsert({
    required String category,
    required double amount,
  }) async {
    final res = await _dio.put('/v1/budgets', data: {
      'category': category,
      'amount': amount,
    });
    return Map<String, dynamic>.from(res.data as Map);
  }
}
