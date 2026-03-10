import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../auth/presentation/auth_controller.dart';
import 'budgets_controller.dart';

class BudgetsScreen extends ConsumerWidget {
  const BudgetsScreen({super.key});

  Future<void> _editBudget(BuildContext context, WidgetRef ref, String category, double current) async {
    final ctrl = TextEditingController(text: current.toStringAsFixed(0));

    final ok = await showDialog<bool>(
          context: context,
          builder: (context) => AlertDialog(
            title: Text('Set budget: $category'),
            content: TextField(
              controller: ctrl,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(border: OutlineInputBorder(), labelText: 'Amount'),
            ),
            actions: [
              TextButton(onPressed: () => Navigator.of(context).pop(false), child: const Text('Cancel')),
              FilledButton(onPressed: () => Navigator.of(context).pop(true), child: const Text('Save')),
            ],
          ),
        ) ??
        false;

    if (!ok) return;

    final amount = double.tryParse(ctrl.text.trim());
    if (amount == null) return;

    await ref.read(budgetsActionsProvider).upsert(category: category, amount: amount);
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final auth = ref.watch(authStateProvider);
    final currency = (auth.user?['currency'] ?? 'KSh').toString();

    final asyncData = ref.watch(currentBudgetsProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Budgets'),
        actions: [
          IconButton(
            tooltip: 'Profile',
            onPressed: () => context.go('/profile'),
            icon: const Icon(Icons.person),
          ),
        ],
      ),
      body: SafeArea(
        child: asyncData.when(
          data: (data) {
            final budgetData = (data['budget_data'] as List).map((e) => Map<String, dynamic>.from(e as Map)).toList();
            final known = (data['known_categories'] as List).map((e) => e.toString()).toList();

            return ListView(
              padding: const EdgeInsets.all(12),
              children: [
                for (final cat in known) ...[
                  _BudgetCard(
                    currency: currency,
                    category: cat,
                    row: budgetData.cast<Map<String, dynamic>?>().firstWhere(
                          (r) => r?['category'] == cat,
                          orElse: () => null,
                        ),
                    onEdit: (limit) => _editBudget(context, ref, cat, limit),
                  ),
                  const SizedBox(height: 10),
                ],
              ],
            );
          },
          loading: () => const Center(child: CircularProgressIndicator()),
          error: (e, _) => Center(child: Text('Failed: $e')),
        ),
      ),
    );
  }
}

class _BudgetCard extends StatelessWidget {
  const _BudgetCard({
    required this.currency,
    required this.category,
    required this.row,
    required this.onEdit,
  });

  final String currency;
  final String category;
  final Map<String, dynamic>? row;
  final void Function(double limit) onEdit;

  @override
  Widget build(BuildContext context) {
    final limit = (row?['limit'] as num?)?.toDouble() ?? 0.0;
    final spent = (row?['spent'] as num?)?.toDouble() ?? 0.0;
    final percent = (row?['percent'] as num?)?.toDouble() ?? 0.0;

    final color = spent > limit && limit > 0 ? Colors.red : Colors.blue;

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    category,
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w700),
                  ),
                ),
                TextButton(
                  onPressed: () => onEdit(limit),
                  child: const Text('Edit'),
                ),
              ],
            ),
            const SizedBox(height: 6),
            Text('Limit: $currency ${limit.toStringAsFixed(0)}'),
            Text('Spent: $currency ${spent.toStringAsFixed(0)}'),
            const SizedBox(height: 8),
            ClipRRect(
              borderRadius: BorderRadius.circular(10),
              child: LinearProgressIndicator(
                value: (percent / 100).clamp(0, 1),
                minHeight: 10,
                backgroundColor: Colors.black12,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
