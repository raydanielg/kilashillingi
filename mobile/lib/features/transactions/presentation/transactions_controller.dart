import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/dio_provider.dart';
import '../data/transactions_repository.dart';

final transactionsRepositoryProvider = Provider<TransactionsRepository>((ref) {
  return TransactionsRepository(ref.watch(dioProvider));
});

final transactionsPageProvider = FutureProvider.family<Map<String, dynamic>, int>((ref, page) async {
  final repo = ref.watch(transactionsRepositoryProvider);
  return repo.list(page: page);
});

final transactionsActionsProvider = Provider<TransactionsActions>((ref) {
  return TransactionsActions(ref.watch(transactionsRepositoryProvider), ref);
});

class TransactionsActions {
  TransactionsActions(this._repo, this._ref);

  final TransactionsRepository _repo;
  final Ref _ref;

  Future<void> create({
    required String type,
    required double amount,
    required String date,
    String? description,
    String? category,
    String? source,
  }) async {
    await _repo.create(
      type: type,
      amount: amount,
      date: date,
      description: description,
      category: category,
      source: source,
    );

    _ref.invalidate(transactionsPageProvider);
    // Also refresh dashboard
    // ignore: invalid_use_of_protected_member
  }

  Future<void> delete(int id) async {
    await _repo.delete(id);
    _ref.invalidate(transactionsPageProvider);
  }
}
