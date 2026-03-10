import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/dio_provider.dart';
import '../../auth/presentation/auth_controller.dart';
import '../data/profile_repository.dart';

final profileRepositoryProvider = Provider<ProfileRepository>((ref) {
  return ProfileRepository(ref.watch(dioProvider));
});

final profileProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final repo = ref.watch(profileRepositoryProvider);
  final data = await repo.getProfile();
  return Map<String, dynamic>.from(data['user'] as Map);
});

class ProfileUpdateResult {
  const ProfileUpdateResult({required this.user, required this.cleared});

  final Map<String, dynamic> user;
  final bool cleared;
}

final profileActionsProvider = Provider<ProfileActions>((ref) {
  return ProfileActions(
    ref.watch(profileRepositoryProvider),
    ref,
  );
});

class ProfileActions {
  ProfileActions(this._repo, this._ref);

  final ProfileRepository _repo;
  final Ref _ref;

  Future<ProfileUpdateResult> update({
    required String name,
    required String email,
    required String phone,
    required String currency,
  }) async {
    try {
      final data = await _repo.updateProfile(
        name: name,
        email: email,
        phone: phone,
        currency: currency,
      );

      final user = Map<String, dynamic>.from(data['user'] as Map);
      final cleared = (data['cleared'] == true);

      // Refresh local providers
      _ref.invalidate(profileProvider);

      // Keep auth state's user in sync
      _ref.read(authStateProvider.notifier).setUser(user);

      return ProfileUpdateResult(user: user, cleared: cleared);
    } on DioException catch (e) {
      throw Exception(_dioErrorMessage(e));
    }
  }

  String _dioErrorMessage(DioException e) {
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
