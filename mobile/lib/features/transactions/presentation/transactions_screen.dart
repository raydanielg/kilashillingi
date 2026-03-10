import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../auth/presentation/auth_controller.dart';
import 'transactions_controller.dart';

class TransactionsScreen extends ConsumerStatefulWidget {
  const TransactionsScreen({super.key});

  @override
  ConsumerState<TransactionsScreen> createState() => _TransactionsScreenState();
}

class _TransactionsScreenState extends ConsumerState<TransactionsScreen> {
  int _page = 1;

  Future<void> _openCreateDialog(BuildContext context) async {
    final amountCtrl = TextEditingController();
    final descCtrl = TextEditingController();
    String type = 'expense';

    final now = DateTime.now();
    final date = '${now.year.toString().padLeft(4, '0')}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')}';

    final ok = await showDialog<bool>(
          context: context,
          builder: (context) {
            return AlertDialog(
              title: const Text('Add Transaction'),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  DropdownButtonFormField<String>(
                    initialValue: type,
                    items: const [
                      DropdownMenuItem(value: 'income', child: Text('Income')),
                      DropdownMenuItem(value: 'expense', child: Text('Expense')),
                    ],
                    onChanged: (v) => type = v ?? 'expense',
                    decoration: const InputDecoration(border: OutlineInputBorder(), labelText: 'Type'),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: amountCtrl,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(border: OutlineInputBorder(), labelText: 'Amount'),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: descCtrl,
                    decoration: const InputDecoration(border: OutlineInputBorder(), labelText: 'Description (optional)'),
                  ),
                  const SizedBox(height: 8),
                  Text('Date: $date'),
                ],
              ),
              actions: [
                TextButton(onPressed: () => Navigator.of(context).pop(false), child: const Text('Cancel')),
                FilledButton(onPressed: () => Navigator.of(context).pop(true), child: const Text('Save')),
              ],
            );
          },
        ) ??
        false;

    if (!ok) return;

    final amount = double.tryParse(amountCtrl.text.trim());
    if (amount == null) return;

    await ref.read(transactionsActionsProvider).create(
          type: type,
          amount: amount,
          date: date,
          description: descCtrl.text.trim().isEmpty ? null : descCtrl.text.trim(),
        );
  }

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authStateProvider);
    final currency = (auth.user?['currency'] ?? 'KSh').toString();
    final dataAsync = ref.watch(transactionsPageProvider(_page));

    return Scaffold(
      appBar: AppBar(
        title: const Text('Transactions'),
        actions: [
          IconButton(
            tooltip: 'Profile',
            onPressed: () => context.go('/profile'),
            icon: const Icon(Icons.person),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _openCreateDialog(context),
        child: const Icon(Icons.add),
      ),
      body: SafeArea(
        child: dataAsync.when(
          data: (data) {
            final items = (data['data'] as List).map((e) => Map<String, dynamic>.from(e as Map)).toList();
            final current = (data['current_page'] as num?)?.toInt() ?? _page;
            final last = (data['last_page'] as num?)?.toInt() ?? current;

            if (items.isEmpty) {
              return const Center(child: Text('No transactions yet.'));
            }

            return Column(
              children: [
                Expanded(
                  child: ListView.separated(
                    padding: const EdgeInsets.all(12),
                    itemCount: items.length,
                    separatorBuilder: (context, index) => const SizedBox(height: 8),
                    itemBuilder: (context, i) {
                      final tx = items[i];
                      final id = (tx['id'] as num).toInt();
                      final type = (tx['type'] ?? '').toString();
                      final amount = (tx['amount'] ?? '').toString();
                      final desc = (tx['description'] ?? '').toString();
                      final date = (tx['date'] ?? '').toString();

                      final color = type == 'income' ? Colors.green : Colors.red;

                      return Dismissible(
                        key: ValueKey('tx_$id'),
                        direction: DismissDirection.endToStart,
                        background: Container(
                          alignment: Alignment.centerRight,
                          padding: const EdgeInsets.only(right: 16),
                          color: Colors.red,
                          child: const Icon(Icons.delete, color: Colors.white),
                        ),
                        confirmDismiss: (_) async {
                          return await showDialog<bool>(
                                context: context,
                                builder: (context) => AlertDialog(
                                  title: const Text('Delete?'),
                                  content: const Text('Unataka kufuta transaction hii?'),
                                  actions: [
                                    TextButton(onPressed: () => Navigator.of(context).pop(false), child: const Text('Hapana')),
                                    FilledButton(onPressed: () => Navigator.of(context).pop(true), child: const Text('Ndio')),
                                  ],
                                ),
                              ) ??
                              false;
                        },
                        onDismissed: (_) async {
                          await ref.read(transactionsActionsProvider).delete(id);
                        },
                        child: Card(
                          child: ListTile(
                            leading: CircleAvatar(
                              backgroundColor: color.withValues(alpha: 0.12),
                              child: Icon(type == 'income' ? Icons.arrow_downward : Icons.arrow_upward, color: color),
                            ),
                            title: Text('$currency $amount'),
                            subtitle: Text(desc.isEmpty ? date : '$desc\n$date'),
                            isThreeLine: desc.isNotEmpty,
                          ),
                        ),
                      );
                    },
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  child: Row(
                    children: [
                      TextButton(
                        onPressed: current > 1
                            ? () => setState(() {
                                  _page = current - 1;
                                })
                            : null,
                        child: const Text('Prev'),
                      ),
                      const Spacer(),
                      Text('Page $current / $last'),
                      const Spacer(),
                      TextButton(
                        onPressed: current < last
                            ? () => setState(() {
                                  _page = current + 1;
                                })
                            : null,
                        child: const Text('Next'),
                      ),
                    ],
                  ),
                ),
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
