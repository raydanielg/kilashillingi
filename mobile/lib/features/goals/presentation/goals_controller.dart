import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/dio_provider.dart';
import '../data/goals_repository.dart';

final goalsRepositoryProvider = Provider<GoalsRepository>((ref) {
  return GoalsRepository(ref.watch(dioProvider));
});

final goalsListProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final repo = ref.watch(goalsRepositoryProvider);
  final res = await repo.list(perPage: 100);
  final raw = res['data'];
  if (raw is List) {
    return raw.map((e) => Map<String, dynamic>.from(e as Map)).toList();
  }
  return <Map<String, dynamic>>[];
});

final goalsActionsProvider = Provider<GoalsActions>((ref) {
  return GoalsActions(ref.watch(goalsRepositoryProvider), ref);
});

final goalInstallmentsProvider = FutureProvider.family<List<Map<String, dynamic>>, int>((ref, goalId) async {
  final repo = ref.watch(goalsRepositoryProvider);
  final res = await repo.listInstallments(goalId);
  final raw = res['installments'];
  if (raw is List) {
    return raw.map((e) => Map<String, dynamic>.from(e as Map)).toList();
  }
  return <Map<String, dynamic>>[];
});

class GoalsActions {
  GoalsActions(this._repo, this._ref);

  final GoalsRepository _repo;
  final Ref _ref;

  Future<void> refresh() async {
    _ref.invalidate(goalsListProvider);
  }

  Future<void> create({
    required String title,
    required double targetAmount,
    double? currentAmount,
    String? description,
    String? startDate,
    String? dueDate,
  }) async {
    await _repo.create(
      title: title,
      targetAmount: targetAmount,
      currentAmount: currentAmount,
      description: description,
      startDate: startDate,
      dueDate: dueDate,
    );
    _ref.invalidate(goalsListProvider);
  }

  Future<void> update(int id, {
    String? title,
    String? description,
    double? targetAmount,
    double? currentAmount,
    String? startDate,
    String? dueDate,
    String? status,
  }) async {
    await _repo.update(
      id,
      title: title,
      description: description,
      targetAmount: targetAmount,
      currentAmount: currentAmount,
      startDate: startDate,
      dueDate: dueDate,
      status: status,
    );
    _ref.invalidate(goalsListProvider);
  }

  Future<void> delete(int id) async {
    await _repo.delete(id);
    _ref.invalidate(goalsListProvider);
  }

  Future<void> addInstallment(
    int goalId, {
    required double amount,
    required String date,
    String? note,
  }) async {
    await _repo.addInstallment(goalId, amount: amount, date: date, note: note);
    _ref.invalidate(goalsListProvider);
    _ref.invalidate(goalInstallmentsProvider(goalId));
  }

  Future<void> deleteInstallment(int goalId, int installmentId) async {
    await _repo.deleteInstallment(installmentId);
    _ref.invalidate(goalsListProvider);
    _ref.invalidate(goalInstallmentsProvider(goalId));
  }
}
