import 'package:dio/dio.dart';

class DashboardRepository {
  DashboardRepository(this._dio);

  final Dio _dio;

  Future<Map<String, dynamic>> summary() async {
    try {
      final res = await _dio.get('/v1/dashboard/summary');
      return Map<String, dynamic>.from(res.data as Map);
    } on DioException catch (e) {
      throw Exception(_dioErrorMessage(e));
    }
  }

  String _dioErrorMessage(DioException e) {
    final status = e.response?.statusCode;
    final data = e.response?.data;

    final raw = <String>[
      e.message ?? '',
      if (data is Map && data['message'] != null) data['message'].toString() else '',
      if (data is String) data else '',
    ].join(' ');

    if (status == 401) {
      return 'Session ime-expire. Tafadhali login tena.';
    }

    final lower = raw.toLowerCase();
    if (status == 500 && (lower.contains('auth driver') && lower.contains('sanctum') && lower.contains('not defined'))) {
      return 'Server ina hitilafu ya login (Sanctum haijawekwa vizuri).\n'
          'Tafadhali mwambie admin afanye: composer install, php artisan optimize:clear, '
          'php artisan migrate (na kuhakikisha laravel/sanctum ipo production).';
    }

    if (data is Map) {
      final msg = data['message']?.toString();
      final errors = data['errors'];

      if (errors is Map) {
        final messages = <String>[];
        for (final entry in errors.entries) {
          final v = entry.value;
          if (v is List) {
            messages.addAll(v.map((x) => x.toString()));
          } else if (v != null) {
            messages.add(v.toString());
          }
        }

        if (messages.isNotEmpty) {
          return messages.join('\n');
        }
      }

      if (msg != null && msg.isNotEmpty) {
        return status != null ? '($status) $msg' : msg;
      }
    }

    if (status != null) {
      return '($status) Dashboard request failed.';
    }

    return 'Dashboard request failed: ${e.message ?? e.toString()}';
  }
}
