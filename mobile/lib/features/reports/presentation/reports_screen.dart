import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'reports_controller.dart';

class ReportsScreen extends ConsumerWidget {
  const ReportsScreen({super.key, this.embedded = false});

  final bool embedded;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;

    final type = ref.watch(reportTypeProvider);
    final asyncPreview = ref.watch(reportPreviewProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    'Ripoti',
                    style: theme.textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900),
                  ),
                ),
                IconButton(
                  tooltip: 'Refresh',
                  onPressed: () => ref.read(reportActionsProvider).refresh(),
                  icon: Icon(Icons.refresh, color: primary),
                ),
              ],
            ),
            const SizedBox(height: 10),
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  _TypeChip(
                    label: 'Leo',
                    icon: Icons.today,
                    selected: type == 'today',
                    color: Colors.blue,
                    onTap: () => ref.read(reportActionsProvider).setType('today'),
                  ),
                  const SizedBox(width: 8),
                  _TypeChip(
                    label: 'Wiki',
                    icon: Icons.date_range,
                    selected: type == 'week',
                    color: Colors.green,
                    onTap: () => ref.read(reportActionsProvider).setType('week'),
                  ),
                  const SizedBox(width: 8),
                  _TypeChip(
                    label: 'Mwezi',
                    icon: Icons.calendar_month,
                    selected: type == 'month',
                    color: Colors.orange,
                    onTap: () => ref.read(reportActionsProvider).setType('month'),
                  ),
                  const SizedBox(width: 8),
                  _TypeChip(
                    label: 'Mwaka',
                    icon: Icons.analytics,
                    selected: type == 'year',
                    color: Colors.purple,
                    onTap: () => ref.read(reportActionsProvider).setType('year'),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),
            Expanded(
              child: asyncPreview.when(
                data: (data) {
                  final title = (data['title'] ?? '').toString();
                  final totals = (data['totals'] is Map) ? Map<String, dynamic>.from(data['totals'] as Map) : <String, dynamic>{};
                  final income = (totals['income'] as num?)?.toDouble() ?? 0;
                  final expense = (totals['expense'] as num?)?.toDouble() ?? 0;
                  final balance = (totals['balance'] as num?)?.toDouble() ?? 0;
                  final comment = (data['comment'] ?? '').toString();

                  final pieRaw = data['pie'];
                  final pie = pieRaw is List
                      ? pieRaw.map((e) => Map<String, dynamic>.from(e as Map)).toList()
                      : <Map<String, dynamic>>[];

                  final txRaw = data['transactions'];
                  final txs = txRaw is List
                      ? txRaw.map((e) => Map<String, dynamic>.from(e as Map)).toList()
                      : <Map<String, dynamic>>[];

                  return ListView(
                    padding: EdgeInsets.zero,
                    children: [
                      if (title.isNotEmpty)
                        Text(
                          title,
                          style: theme.textTheme.titleSmall?.copyWith(
                            fontWeight: FontWeight.w900,
                            color: theme.colorScheme.onSurface.withValues(alpha: 0.85),
                          ),
                        ),
                      if (title.isNotEmpty) const SizedBox(height: 10),
                      Row(
                        children: [
                          Expanded(
                            child: _SummaryCard(
                              label: 'Mapato',
                              value: income,
                              color: Colors.green,
                              icon: Icons.add_chart,
                            ),
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: _SummaryCard(
                              label: 'Matumizi',
                              value: expense,
                              color: Colors.red,
                              icon: Icons.payments_outlined,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 10),
                      _SummaryCard(
                        label: 'Salio',
                        value: balance,
                        color: balance >= 0 ? Colors.indigo : Colors.deepOrange,
                        icon: Icons.account_balance_wallet_outlined,
                        wide: true,
                      ),
                      const SizedBox(height: 12),
                      _CommentCard(comment: comment),
                      const SizedBox(height: 12),
                      if (pie.isNotEmpty) ...[
                        Text('Matumizi kwa Makundi', style: theme.textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w900)),
                        const SizedBox(height: 8),
                        ...pie.take(8).map((row) {
                          final cat = (row['category'] ?? '').toString();
                          final tot = (row['total'] as num?)?.toDouble() ?? 0;
                          return Padding(
                            padding: const EdgeInsets.only(bottom: 8),
                            child: _CategoryRow(category: cat, total: tot),
                          );
                        }),
                        const SizedBox(height: 8),
                      ],
                      Text('Miamala', style: theme.textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w900)),
                      const SizedBox(height: 8),
                      if (txs.isEmpty)
                        Text('Hakuna miamala kwenye kipindi hiki.', style: theme.textTheme.bodySmall?.copyWith(color: theme.hintColor))
                      else
                        ...txs.take(25).map((tx) => _TransactionRow(tx: tx)),
                      const SizedBox(height: 24),
                    ],
                  );
                },
                loading: () => const Center(child: CircularProgressIndicator()),
                error: (e, _) => Center(
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text('Imeshindikana kupakia ripoti:\n$e', textAlign: TextAlign.center),
                      const SizedBox(height: 12),
                      FilledButton(
                        onPressed: () => ref.read(reportActionsProvider).refresh(),
                        child: const Text('Jaribu tena'),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _TypeChip extends StatelessWidget {
  const _TypeChip({
    required this.label,
    required this.icon,
    required this.selected,
    required this.color,
    required this.onTap,
  });

  final String label;
  final IconData icon;
  final bool selected;
  final Color color;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final bg = selected ? color.withValues(alpha: 0.16) : theme.colorScheme.surfaceContainerHighest;
    final border = selected ? color.withValues(alpha: 0.35) : theme.colorScheme.outlineVariant.withValues(alpha: 0.35);
    final fg = selected ? color : theme.colorScheme.onSurface.withValues(alpha: 0.85);

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(999),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        decoration: BoxDecoration(
          color: bg,
          borderRadius: BorderRadius.circular(999),
          border: Border.all(color: border),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 16, color: fg),
            const SizedBox(width: 6),
            Text(label, style: TextStyle(fontWeight: FontWeight.w900, color: fg)),
          ],
        ),
      ),
    );
  }
}

class _SummaryCard extends StatelessWidget {
  const _SummaryCard({
    required this.label,
    required this.value,
    required this.color,
    required this.icon,
    this.wide = false,
  });

  final String label;
  final double value;
  final Color color;
  final IconData icon;
  final bool wide;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: surface,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: outline),
      ),
      child: Row(
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.14),
              borderRadius: BorderRadius.circular(14),
            ),
            child: Icon(icon, color: color),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor, fontWeight: FontWeight.w800)),
                const SizedBox(height: 2),
                Text(
                  value.toStringAsFixed(0),
                  style: theme.textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _CommentCard extends StatelessWidget {
  const _CommentCard({required this.comment});

  final String comment;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: surface,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: outline),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(Icons.auto_awesome, color: primary),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              comment.isEmpty ? '—' : comment,
              style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w700),
            ),
          ),
        ],
      ),
    );
  }
}

class _CategoryRow extends StatelessWidget {
  const _CategoryRow({required this.category, required this.total});

  final String category;
  final double total;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: outline),
      ),
      child: Row(
        children: [
          Expanded(
            child: Text(category, style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w800)),
          ),
          Text(total.toStringAsFixed(0), style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w900)),
        ],
      ),
    );
  }
}

class _TransactionRow extends StatelessWidget {
  const _TransactionRow({required this.tx});

  final Map<String, dynamic> tx;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

    final type = (tx['type'] ?? '').toString();
    final desc = (tx['description'] ?? '').toString();
    final time = (tx['time'] ?? '').toString();
    final amount = (tx['amount'] as num?)?.toDouble() ?? 0;

    final isIncome = type == 'income';
    final color = isIncome ? Colors.green : Colors.red;

    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: outline),
        ),
        child: Row(
          children: [
            Container(
              width: 38,
              height: 38,
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.12),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Icon(isIncome ? Icons.add : Icons.remove, color: color),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(desc.isEmpty ? '-' : desc, maxLines: 1, overflow: TextOverflow.ellipsis, style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w800)),
                  const SizedBox(height: 2),
                  Text(time.isEmpty ? (isIncome ? 'IN' : 'OUT') : '$time • ${isIncome ? 'IN' : 'OUT'}', style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor)),
                ],
              ),
            ),
            Text(
              amount.toStringAsFixed(0),
              style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w900, color: color),
            ),
          ],
        ),
      ),
    );
  }
}
