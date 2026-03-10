import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/network/dio_provider.dart';
import '../data/meta_repository.dart';

final metaRepositoryProvider = Provider<MetaRepository>((ref) {
  return MetaRepository(ref.watch(dioProvider));
});

final currenciesProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  return ref.watch(metaRepositoryProvider).currencies();
});
