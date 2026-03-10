import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/dio_provider.dart';
import '../data/budgets_repository.dart';

final budgetsRepositoryProvider = Provider<BudgetsRepository>((ref) {
  return BudgetsRepository(ref.watch(dioProvider));
});

final currentBudgetsProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final repo = ref.watch(budgetsRepositoryProvider);
  return repo.current();
});

final budgetsActionsProvider = Provider<BudgetsActions>((ref) {
  return BudgetsActions(ref.watch(budgetsRepositoryProvider), ref);
});

class BudgetsActions {
  BudgetsActions(this._repo, this._ref);

  final BudgetsRepository _repo;
  final Ref _ref;

  Future<void> upsert({required String category, required double amount}) async {
    await _repo.upsert(category: category, amount: amount);
    _ref.invalidate(currentBudgetsProvider);
  }
}
