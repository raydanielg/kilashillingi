import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../core/presentation/dashboard_controller.dart';
import 'transactions_controller.dart';

class TransactionsScreen extends ConsumerStatefulWidget {
  const TransactionsScreen({
    super.key,
    this.embedded = false,
    this.typeFilter,
  });

  final bool embedded;
  final String? typeFilter;

  @override
  ConsumerState<TransactionsScreen> createState() => _TransactionsScreenState();
}

class _TransactionsScreenState extends ConsumerState<TransactionsScreen> {
  int _page = 1;

  String _fmtMoney(String currency, double v) => '$currency ${v.toStringAsFixed(0)}';

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
    final t = widget.typeFilter?.trim().toLowerCase();

    final dashAsync = ref.watch(dashboardSummaryProvider);
    final dataAsync = (t == 'income' || t == 'expense')
        ? ref.watch(transactionsPageFilteredProvider((page: _page, type: t)))
        : ref.watch(transactionsPageProvider(_page));

    final body = dataAsync.when(
          data: (data) {
            final items = (data['data'] as List).map((e) => Map<String, dynamic>.from(e as Map)).toList();
            final current = (data['current_page'] as num?)?.toInt() ?? _page;
            final last = (data['last_page'] as num?)?.toInt() ?? current;

            final title = t == 'income'
                ? 'Mapato'
                : t == 'expense'
                    ? 'Matumizi'
                    : 'Miamala';

            if (items.isEmpty) {
              return Center(
                child: Text(
                  t == 'income'
                      ? 'Bado hakuna mapato.'
                      : t == 'expense'
                          ? 'Bado hakuna matumizi.'
                          : 'Bado hakuna miamala.',
                ),
              );
            }

            final pageTotal = items.fold<double>(0.0, (sum, tx) {
              final v = tx['amount'];
              if (v is num) return sum + v.toDouble();
              return sum + (double.tryParse(v?.toString() ?? '') ?? 0.0);
            });

            return Column(
              children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(12, 12, 12, 6),
                  child: _TxSummaryHeader(
                    title: title,
                    currency: currency,
                    type: t,
                    pageCount: items.length,
                    pageTotal: pageTotal,
                    dashboardAsync: dashAsync,
                  ),
                ),
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
                      final theme = Theme.of(context);
                      final surface = theme.colorScheme.surfaceContainerHighest;
                      final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

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
                        child: Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: surface,
                            borderRadius: BorderRadius.circular(18),
                            border: Border.all(color: outline),
                          ),
                          child: Row(
                            children: [
                              Container(
                                width: 42,
                                height: 42,
                                decoration: BoxDecoration(
                                  color: color.withValues(alpha: 0.12),
                                  borderRadius: BorderRadius.circular(16),
                                ),
                                child: Icon(type == 'income' ? Icons.call_received : Icons.call_made, color: color),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      desc.isEmpty ? (type == 'income' ? 'Mapato' : 'Matumizi') : desc,
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                      style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w900),
                                    ),
                                    const SizedBox(height: 2),
                                    Text(
                                      date,
                                      style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor),
                                    ),
                                  ],
                                ),
                              ),
                              const SizedBox(width: 10),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.end,
                                children: [
                                  Text(
                                    '$currency $amount',
                                    style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w900, color: color),
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    type == 'income' ? 'IN' : 'OUT',
                                    style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor, fontWeight: FontWeight.w800),
                                  ),
                                ],
                              ),
                            ],
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
                      TextButton.icon(
                        onPressed: current < last
                            ? () => setState(() {
                                  _page = current + 1;
                                })
                            : null,
                        icon: const Icon(Icons.chevron_right),
                        label: const Text('Next'),
                      ),
                      const Spacer(),
                      Text('Page $current / $last'),
                      const Spacer(),
                      TextButton.icon(
                        onPressed: current > 1
                            ? () => setState(() {
                                  _page = current - 1;
                                })
                            : null,
                        icon: const Icon(Icons.chevron_left),
                        label: const Text('Prev'),
                      ),
                    ],
                  ),
                ),
              ],
            );
          },
          loading: () => const Center(child: CircularProgressIndicator()),
          error: (e, _) => Center(child: Text('Failed: $e')),
        );

    if (widget.embedded) {
      return Stack(
        children: [
          SafeArea(child: body),
        ],
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: Text(
          t == 'income'
              ? 'Mapato'
              : t == 'expense'
                  ? 'Matumizi'
                  : 'Miamala',
        ),
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
        heroTag: t == null ? 'tx_fab_all' : 'tx_fab_$t',
        child: const Icon(Icons.add),
      ),
      body: SafeArea(child: body),
    );
  }
}

class _TxSummaryHeader extends StatelessWidget {
  const _TxSummaryHeader({
    required this.title,
    required this.currency,
    required this.type,
    required this.pageCount,
    required this.pageTotal,
    required this.dashboardAsync,
  });

  final String title;
  final String currency;
  final String? type;
  final int pageCount;
  final double pageTotal;
  final AsyncValue<Map<String, dynamic>> dashboardAsync;

  double _asDouble(dynamic v) {
    if (v == null) return 0;
    if (v is num) return v.toDouble();
    return double.tryParse(v.toString()) ?? 0;
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

    final color = type == 'income' ? Colors.green : type == 'expense' ? Colors.red : primary;

    double today = 0;
    double month = 0;
    int todayCount = 0;
    int monthCount = 0;

    dashboardAsync.whenData((data) {
      final totals = data['totals'] is Map ? Map<String, dynamic>.from(data['totals'] as Map) : <String, dynamic>{};
      if (type == 'income') {
        today = _asDouble(totals['today_income']);
        month = _asDouble(totals['month_income']);
      } else if (type == 'expense') {
        today = _asDouble(totals['today_expense']);
        month = _asDouble(totals['month_expense']);
      }
      todayCount = (totals['today_transactions_count'] as num?)?.toInt() ?? 0;
      monthCount = (totals['month_transactions_count'] as num?)?.toInt() ?? 0;
    });

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: surface,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: outline),
        gradient: LinearGradient(
          colors: [
            color.withValues(alpha: 0.10),
            primary.withValues(alpha: 0.06),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.14),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Icon(type == 'income' ? Icons.add_chart : Icons.payments_outlined, color: color),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title, style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900)),
                    Text('Muhtasari wa haraka', style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor)),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(999),
                ),
                child: Text('$pageCount items', style: TextStyle(fontWeight: FontWeight.w900, color: color, fontSize: 12)),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(child: _MiniStat(label: 'Jumla (page)', value: '$currency ${pageTotal.toStringAsFixed(0)}', valueColor: color)),
              const SizedBox(width: 10),
              Expanded(child: _MiniStat(label: 'Leo', value: '$currency ${today.toStringAsFixed(0)}')),
              const SizedBox(width: 10),
              Expanded(child: _MiniStat(label: 'Mwezi', value: '$currency ${month.toStringAsFixed(0)}')),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            'Leo: $todayCount miamala • Mwezi: $monthCount miamala',
            style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor),
          ),
        ],
      ),
    );
  }
}

class _MiniStat extends StatelessWidget {
  const _MiniStat({required this.label, required this.value, this.valueColor});

  final String label;
  final String value;
  final Color? valueColor;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor, fontWeight: FontWeight.w800)),
        const SizedBox(height: 2),
        Text(
          value,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w900, color: valueColor),
        ),
      ],
    );
  }
}
