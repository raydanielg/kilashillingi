import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/dio_provider.dart';
import '../data/dashboard_repository.dart';

final dashboardRepositoryProvider = Provider<DashboardRepository>((ref) {
  return DashboardRepository(ref.watch(dioProvider));
});

final dashboardSummaryProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final repo = ref.watch(dashboardRepositoryProvider);
  return repo.summary();
});
