import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/dio_provider.dart';
import '../../../core/storage/token_storage.dart';
import '../data/auth_repository.dart';
import 'auth_state.dart';
import '../../../core/config/api_config.dart';

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return AuthRepository(ref.watch(dioProvider));
});

final authStateProvider = StateNotifierProvider<AuthController, AuthState>((ref) {
  return AuthController(
    ref.watch(authRepositoryProvider),
    ref.watch(tokenStorageProvider),
  );
});

class AuthController extends StateNotifier<AuthState> {
  AuthController(this._repo, this._tokenStorage) : super(AuthState.unknown);

  final AuthRepository _repo;
  final TokenStorage _tokenStorage;

  Future<void> bootstrap() async {
    state = state.copyWith(isLoading: true, error: null);

    final token = await _tokenStorage.readToken();
    if (token == null || token.isEmpty) {
      state = AuthState.unauthenticated;
      return;
    }

    final cachedUser = await _tokenStorage.readUser();
    if (cachedUser != null) {
      state = AuthState(
        status: AuthStatus.authenticated,
        user: cachedUser,
        isLoading: true,
      );
    }

    try {
      final data = await _repo.me();
      final user = Map<String, dynamic>.from(data['user'] as Map);
      await _tokenStorage.writeUser(user);

      state = AuthState(
        status: AuthStatus.authenticated,
        user: user,
      );
    } on DioException catch (e) {
      final status = e.response?.statusCode;
      final msg = _dioErrorMessage(e);

      if (status == 401) {
        await _tokenStorage.deleteToken();
        await _tokenStorage.deleteUser();
        state = AuthState(
          status: AuthStatus.unauthenticated,
          error: msg,
        );
        return;
      }

      if (cachedUser != null) {
        state = AuthState(
          status: AuthStatus.authenticated,
          user: cachedUser,
          error: msg,
          isLoading: false,
        );
      } else {
        state = AuthState(
          status: AuthStatus.unauthenticated,
          error: msg,
          isLoading: false,
        );
      }
    } catch (e) {
      if (cachedUser != null) {
        state = AuthState(
          status: AuthStatus.authenticated,
          user: cachedUser,
          error: e.toString(),
          isLoading: false,
        );
      } else {
        state = AuthState(
          status: AuthStatus.unauthenticated,
          error: e.toString(),
          isLoading: false,
        );
      }
    }
  }

  Future<bool> login({required String login, required String password}) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      final data = await _repo.login(login: login, password: password);
      final token = (data['token'] ?? '').toString();
      if (token.isEmpty) {
        state = state.copyWith(isLoading: false, error: 'Missing token from server.');
        return false;
      }

      await _tokenStorage.writeToken(token);
      final user = Map<String, dynamic>.from(data['user'] as Map);
      await _tokenStorage.writeUser(user);
      state = AuthState(
        status: AuthStatus.authenticated,
        user: user,
      );
      return true;
    } on DioException catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: _dioErrorMessage(e),
      );
      return false;
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String phone,
    required String currency,
    required String password,
    required String passwordConfirmation,
  }) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      final data = await _repo.register(
        name: name,
        email: email,
        phone: phone,
        currency: currency,
        password: password,
        passwordConfirmation: passwordConfirmation,
      );

      final token = (data['token'] ?? '').toString();
      if (token.isEmpty) {
        state = state.copyWith(isLoading: false, error: 'Missing token from server.');
        return false;
      }

      await _tokenStorage.writeToken(token);
      final user = Map<String, dynamic>.from(data['user'] as Map);
      await _tokenStorage.writeUser(user);
      state = AuthState(
        status: AuthStatus.authenticated,
        user: user,
      );
      return true;
    } on DioException catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: _dioErrorMessage(e),
      );
      return false;
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
      return false;
    }
  }

  Future<void> logout() async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      await _repo.logout();
    } catch (_) {}

    await _tokenStorage.deleteToken();
    await _tokenStorage.deleteUser();
    state = AuthState.unauthenticated;
  }

  void setUser(Map<String, dynamic> user) {
    if (state.status != AuthStatus.authenticated) {
      return;
    }

    state = state.copyWith(user: user, error: null, isLoading: false);
  }

  String _dioErrorMessage(DioException e) {
    if (e.type == DioExceptionType.connectionError || e.type == DioExceptionType.unknown) {
      final baseUrl = ApiConfig.baseUrl;
      final hostHint = baseUrl.contains('127.0.0.1')
          ? 'Ukiwa kwenye REAL PHONE, 127.0.0.1 ni simu yenyewe. Tumia IP ya PC (mf http://192.168.x.x:8000/api) au tumia adb reverse: adb reverse tcp:8000 tcp:8000'
          : 'Hakikisha Laravel server iko running na simu/emulator ina-access hiyo address.';

      return 'Imeshindikana ku-connect kwenye server.\nBase URL: $baseUrl\n$hostHint';
    }

    final data = e.response?.data;
    if (data is Map) {
      final msg = data['message']?.toString();
      final errors = data['errors'];

      if (errors is Map) {
        final messages = <String>[];
        for (final entry in errors.entries) {
          final v = entry.value;
          if (v is List) {
            for (final item in v) {
              final s = item?.toString().trim();
              if (s != null && s.isNotEmpty) messages.add(s);
            }
          } else {
            final s = v?.toString().trim();
            if (s != null && s.isNotEmpty) messages.add(s);
          }
        }
        if (messages.isNotEmpty) {
          return messages.join('\n');
        }
      }

      if (msg != null && msg.trim().isNotEmpty) {
        return msg;
      }
    }

    return e.message ?? 'Request failed.';
  }
}
