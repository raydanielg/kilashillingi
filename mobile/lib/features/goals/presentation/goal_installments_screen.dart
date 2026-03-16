import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'goals_controller.dart';

class GoalInstallmentsScreen extends ConsumerWidget {
  const GoalInstallmentsScreen({
    super.key,
    required this.goalId,
    required this.goalTitle,
    required this.currency,
  });

  final int goalId;
  final String goalTitle;
  final String currency;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final theme = Theme.of(context);
    final asyncItems = ref.watch(goalInstallmentsProvider(goalId));

    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(goalTitle.isEmpty ? 'Installments' : goalTitle),
            Text('Historia ya installments', style: theme.textTheme.labelSmall),
          ],
        ),
      ),
      body: asyncItems.when(
        data: (items) {
          if (items.isEmpty) {
            return const Center(child: Text('Bado hakuna installments.'));
          }

          return ListView.separated(
            padding: const EdgeInsets.all(12),
            itemCount: items.length,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (context, i) {
              final it = items[i];
              final id = (it['id'] as num?)?.toInt() ?? 0;
              final date = (it['date'] ?? '').toString();
              final note = (it['note'] ?? '').toString();

              double amount = 0;
              final v = it['amount'];
              if (v is num) amount = v.toDouble();
              if (v is String) amount = double.tryParse(v) ?? 0;

              return Card(
                elevation: 0,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                  side: BorderSide(color: theme.dividerColor.withValues(alpha: 0.08)),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(14),
                  child: Row(
                    children: [
                      Container(
                        width: 44,
                        height: 44,
                        decoration: BoxDecoration(
                          color: Colors.green.withValues(alpha: 0.12),
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: const Icon(Icons.savings_outlined, color: Colors.green),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              '$currency ${amount.toStringAsFixed(0)}',
                              style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              date.isEmpty ? '-' : date,
                              style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor),
                            ),
                            if (note.trim().isNotEmpty) ...[
                              const SizedBox(height: 6),
                              Text(note, style: theme.textTheme.bodySmall),
                            ]
                          ],
                        ),
                      ),
                      IconButton(
                        tooltip: 'Futa installment',
                        onPressed: () async {
                          final ok = await showDialog<bool>(
                                context: context,
                                builder: (_) => AlertDialog(
                                  title: const Text('Futa?'),
                                  content: const Text('Una uhakika unataka kufuta installment hii?'),
                                  actions: [
                                    TextButton(onPressed: () => Navigator.of(context).pop(false), child: const Text('Ghairi')),
                                    FilledButton(onPressed: () => Navigator.of(context).pop(true), child: const Text('Futa')),
                                  ],
                                ),
                              ) ??
                              false;
                          if (!ok) return;
                          if (id <= 0) return;
                          await ref.read(goalsActionsProvider).deleteInstallment(goalId, id);
                        },
                        icon: Icon(Icons.delete_outline, color: Colors.red.shade700),
                      ),
                    ],
                  ),
                ),
              );
            },
          );
        },
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Imeshindikana: $e')),
      ),
    );
  }
}
