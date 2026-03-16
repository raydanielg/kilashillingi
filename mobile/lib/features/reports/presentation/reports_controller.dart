import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/dio_provider.dart';
import '../data/reports_repository.dart';

final reportsRepositoryProvider = Provider<ReportsRepository>((ref) {
  return ReportsRepository(ref.watch(dioProvider));
});

final reportTypeProvider = StateProvider<String>((ref) => 'today');

final reportPreviewProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final type = ref.watch(reportTypeProvider);
  final repo = ref.watch(reportsRepositoryProvider);
  return repo.preview(type: type);
});

final reportActionsProvider = Provider<ReportActions>((ref) {
  return ReportActions(ref);
});

class ReportActions {
  ReportActions(this._ref);

  final Ref _ref;

  void setType(String type) {
    _ref.read(reportTypeProvider.notifier).state = type;
  }

  void refresh() {
    _ref.invalidate(reportPreviewProvider);
  }
}
