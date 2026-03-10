import 'package:dio/dio.dart';

class TransactionsRepository {
  TransactionsRepository(this._dio);

  final Dio _dio;

  Future<Map<String, dynamic>> list({int page = 1, int perPage = 20}) async {
    final res = await _dio.get(
      '/v1/transactions',
      queryParameters: {
        'page': page,
        'per_page': perPage,
      },
    );
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<Map<String, dynamic>> create({
    required String type,
    required double amount,
    required String date,
    String? description,
    String? category,
    String? source,
  }) async {
    final res = await _dio.post(
      '/v1/transactions',
      data: {
        'type': type,
        'amount': amount,
        'date': date,
        'description': description,
        'category': category,
        'source': source,
      },
    );
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<void> delete(int id) async {
    await _dio.delete('/v1/transactions/$id');
  }
}
