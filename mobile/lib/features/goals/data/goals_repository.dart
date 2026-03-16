import 'package:dio/dio.dart';

class GoalsRepository {
  GoalsRepository(this._dio);

  final Dio _dio;

  Future<Map<String, dynamic>> list({
    int page = 1,
    int perPage = 50,
    String? status,
  }) async {
    final s = status?.trim().toLowerCase();
    final res = await _dio.get(
      '/v1/goals',
      queryParameters: {
        'page': page,
        'per_page': perPage,
        if (s == 'active' || s == 'completed' || s == 'paused') 'status': s,
      },
    );
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<Map<String, dynamic>> create({
    required String title,
    required double targetAmount,
    double? currentAmount,
    String? description,
    String? startDate,
    String? dueDate,
  }) async {
    final res = await _dio.post(
      '/v1/goals',
      data: {
        'title': title,
        'description': description,
        'target_amount': targetAmount,
        'current_amount': currentAmount,
        'start_date': startDate,
        'due_date': dueDate,
      },
    );
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<Map<String, dynamic>> update(int id, {
    String? title,
    String? description,
    double? targetAmount,
    double? currentAmount,
    String? startDate,
    String? dueDate,
    String? status,
  }) async {
    final res = await _dio.put(
      '/v1/goals/$id',
      data: {
        if (title != null) 'title': title,
        if (description != null) 'description': description,
        if (targetAmount != null) 'target_amount': targetAmount,
        if (currentAmount != null) 'current_amount': currentAmount,
        if (startDate != null) 'start_date': startDate,
        if (dueDate != null) 'due_date': dueDate,
        if (status != null) 'status': status,
      },
    );
    return Map<String, dynamic>.from(res.data as Map);
  }

  Future<void> delete(int id) async {
    await _dio.delete('/v1/goals/$id');
  }
}
