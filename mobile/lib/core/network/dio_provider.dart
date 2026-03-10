import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../config/api_config.dart';
import '../storage/token_storage.dart';

final secureStorageProvider = Provider<FlutterSecureStorage>((ref) {
  return const FlutterSecureStorage();
});

final tokenStorageProvider = Provider<TokenStorage>((ref) {
  return TokenStorage(ref.watch(secureStorageProvider));
});

final dioProvider = Provider<Dio>((ref) {
  final tokenStorage = ref.watch(tokenStorageProvider);

  final dio = Dio(
    BaseOptions(
      baseUrl: ApiConfig.baseUrl,
      connectTimeout: const Duration(seconds: 60),
      sendTimeout: const Duration(seconds: 60),
      receiveTimeout: const Duration(seconds: 60),
      headers: {
        'Accept': 'application/json',
      },
    ),
  );

  dio.interceptors.add(
    InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await tokenStorage.readToken();
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) async {
        final req = error.requestOptions;
        final alreadyRetried = (req.extra['__retried'] == true);

        final shouldRetry = !alreadyRetried &&
            (error.type == DioExceptionType.connectionTimeout ||
                error.type == DioExceptionType.sendTimeout ||
                error.type == DioExceptionType.receiveTimeout ||
                error.type == DioExceptionType.connectionError);

        if (shouldRetry) {
          req.extra['__retried'] = true;
          try {
            await Future<void>.delayed(const Duration(milliseconds: 600));
            final response = await dio.fetch(req);
            return handler.resolve(response);
          } catch (e) {
            // fall through
          }
        }

        handler.next(error);
      },
    ),
  );

  return dio;
});
