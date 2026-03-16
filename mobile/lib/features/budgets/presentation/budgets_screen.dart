import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../auth/presentation/auth_controller.dart';
import 'budgets_controller.dart';

class _BudgetDonutPainter extends CustomPainter {
  _BudgetDonutPainter({
    required this.percent,
    required this.trackColor,
    required this.gradientColors,
  });

  final double percent;
  final Color trackColor;
  final List<Color> gradientColors;

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = (size.shortestSide / 2) - 5;
    const stroke = 10.0;

    final trackPaint = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = stroke
      ..strokeCap = StrokeCap.round
      ..color = trackColor;

    canvas.drawCircle(center, radius, trackPaint);

    final p = percent.clamp(0, 1).toDouble();
    if (p <= 0) return;

    final rect = Rect.fromCircle(center: center, radius: radius);
    final sweep = 2 * 3.141592653589793 * p;

    final gradient = SweepGradient(
      startAngle: -3.141592653589793 / 2,
      endAngle: -3.141592653589793 / 2 + sweep,
      colors: gradientColors,
    );

    final progressPaint = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = stroke
      ..strokeCap = StrokeCap.round
      ..shader = gradient.createShader(rect);

    canvas.drawArc(rect, -3.141592653589793 / 2, sweep, false, progressPaint);
  }

  @override
  bool shouldRepaint(covariant _BudgetDonutPainter oldDelegate) {
    return oldDelegate.percent != percent || oldDelegate.trackColor != trackColor;
  }
}

class BudgetsScreen extends ConsumerWidget {
  const BudgetsScreen({super.key, this.embedded = false});

  final bool embedded;

  Future<void> _openAddBudgetSheet(BuildContext context, WidgetRef ref, String currency, List<String> categories) async {
    final amountCtrl = TextEditingController();
    String category = categories.isNotEmpty ? categories.first : 'Mengineyo';

    await showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      showDragHandle: true,
      builder: (context) {
        final theme = Theme.of(context);
        final primary = theme.colorScheme.primary;

        return Padding(
          padding: EdgeInsets.only(
            left: 16,
            right: 16,
            bottom: 16 + MediaQuery.of(context).viewInsets.bottom,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Row(
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: primary.withValues(alpha: 0.12),
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: primary.withValues(alpha: 0.22)),
                    ),
                    child: Icon(Icons.add_circle_outline, color: primary),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Ongeza Bajeti', style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900)),
                        Text('Chagua category na kiasi', style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor)),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                value: category,
                items: categories
                    .map((c) => DropdownMenuItem<String>(
                          value: c,
                          child: Text(c),
                        ))
                    .toList(),
                onChanged: (v) => category = v ?? category,
                decoration: const InputDecoration(border: OutlineInputBorder(), labelText: 'Category'),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: amountCtrl,
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                  border: const OutlineInputBorder(),
                  labelText: 'Kiasi ($currency)',
                ),
              ),
              const SizedBox(height: 12),
              FilledButton.icon(
                onPressed: () async {
                  final amount = double.tryParse(amountCtrl.text.trim());
                  if (amount == null) return;
                  await ref.read(budgetsActionsProvider).upsert(category: category, amount: amount);
                  if (context.mounted) Navigator.of(context).pop();
                },
                icon: const Icon(Icons.check_circle_outline),
                label: const Text('Hifadhi Bajeti'),
              ),
              const SizedBox(height: 10),
            ],
          ),
        );
      },
    );
  }

  Future<void> _editBudget(BuildContext context, WidgetRef ref, String currency, String category, double current) async {
    final ctrl = TextEditingController(text: current.toStringAsFixed(0));

    await showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      showDragHandle: true,
      builder: (context) {
        final theme = Theme.of(context);
        final primary = theme.colorScheme.primary;

        return Padding(
          padding: EdgeInsets.only(
            left: 16,
            right: 16,
            bottom: 16 + MediaQuery.of(context).viewInsets.bottom,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Row(
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: primary.withValues(alpha: 0.12),
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: primary.withValues(alpha: 0.22)),
                    ),
                    child: Icon(Icons.pie_chart_outline, color: primary),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(category, style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900)),
                        Text('Weka budget ya category hii', style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor)),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              TextField(
                controller: ctrl,
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                  border: const OutlineInputBorder(),
                  labelText: 'Kiasi ($currency)',
                ),
              ),
              const SizedBox(height: 12),
              FilledButton.icon(
                onPressed: () async {
                  final amount = double.tryParse(ctrl.text.trim());
                  if (amount == null) return;
                  await ref.read(budgetsActionsProvider).upsert(category: category, amount: amount);
                  if (context.mounted) Navigator.of(context).pop();
                },
                icon: const Icon(Icons.check_circle_outline),
                label: const Text('Hifadhi Budget'),
              ),
              const SizedBox(height: 10),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final auth = ref.watch(authStateProvider);
    final currency = (auth.user?['currency'] ?? 'KSh').toString();

    final asyncData = ref.watch(currentBudgetsProvider);

    final body = asyncData.when(
          data: (data) {
            final budgetData = (data['budget_data'] as List).map((e) => Map<String, dynamic>.from(e as Map)).toList();
            final known = (data['known_categories'] as List).map((e) => e.toString()).toList();

            final totalLimit = budgetData.fold<double>(0.0, (sum, r) => sum + ((r['limit'] as num?)?.toDouble() ?? 0.0));
            final totalSpent = budgetData.fold<double>(0.0, (sum, r) => sum + ((r['spent'] as num?)?.toDouble() ?? 0.0));
            final totalRemaining = (totalLimit - totalSpent).clamp(0.0, double.infinity) as double;

            return ListView(
              padding: const EdgeInsets.all(12),
              children: [
                _BudgetsHeader(
                  currency: currency,
                  totalLimit: totalLimit,
                  totalSpent: totalSpent,
                  totalRemaining: totalRemaining,
                ),
                const SizedBox(height: 10),
                SizedBox(
                  height: 52,
                  child: FilledButton.icon(
                    onPressed: () => _openAddBudgetSheet(context, ref, currency, known),
                    icon: const Icon(Icons.add),
                    label: const Text('Tengeneza Bajeti Mpya'),
                  ),
                ),
                const SizedBox(height: 12),
                for (final cat in known) ...[
                  _BudgetCard(
                    currency: currency,
                    category: cat,
                    row: budgetData.cast<Map<String, dynamic>?>().firstWhere(
                          (r) => r?['category'] == cat,
                          orElse: () => null,
                        ),
                    onEdit: (limit) => _editBudget(context, ref, currency, cat, limit),
                  ),
                  const SizedBox(height: 10),
                ],
              ],
            );
          },
          loading: () => const Center(child: CircularProgressIndicator()),
          error: (e, _) => Center(child: Text('Failed: $e')),
        );

    if (embedded) {
      return SafeArea(child: body);
    }

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
      body: SafeArea(child: body),
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

  double _asDouble(dynamic v) {
    if (v == null) return 0;
    if (v is num) return v.toDouble();
    return double.tryParse(v.toString()) ?? 0;
  }

  @override
  Widget build(BuildContext context) {
    final id = (row?['id'] as num?)?.toInt();
    final limit = (row?['limit'] as num?)?.toDouble() ?? 0.0;
    final spent = (row?['spent'] as num?)?.toDouble() ?? 0.0;
    final percent = (row?['percent'] as num?)?.toDouble() ?? 0.0;
    final remaining = (row?['remaining'] as num?)?.toDouble() ?? (limit - spent);

    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

    final isOver = spent > limit && limit > 0;
    final color = isOver ? Colors.red : primary;
    final pct = (percent / 100).clamp(0, 1).toDouble();

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: surface,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: outline),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              SizedBox(
                width: 44,
                height: 44,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    CustomPaint(
                      size: const Size(44, 44),
                      painter: _BudgetDonutPainter(
                        percent: pct,
                        trackColor: theme.colorScheme.outlineVariant.withValues(alpha: 0.45),
                        gradientColors: <Color>[primary, Colors.purple, color],
                      ),
                    ),
                    Text(
                      '${(pct * 100).toStringAsFixed(0)}%',
                      style: theme.textTheme.labelSmall?.copyWith(fontWeight: FontWeight.w900),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  category,
                  style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: _MiniStat(label: 'Limit', value: '$currency ${limit.toStringAsFixed(0)}'),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _MiniStat(
                  label: 'Spent',
                  value: '$currency ${spent.toStringAsFixed(0)}',
                  valueColor: isOver ? Colors.red : null,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _MiniStat(
                  label: 'Remain',
                  value: '$currency ${remaining.clamp(0, double.infinity).toStringAsFixed(0)}',
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          ClipRRect(
            borderRadius: BorderRadius.circular(999),
            child: LinearProgressIndicator(
              value: pct,
              minHeight: 10,
              backgroundColor: theme.colorScheme.outlineVariant.withValues(alpha: 0.45),
              color: color,
            ),
          ),
          const SizedBox(height: 10),
          Align(
            alignment: Alignment.centerRight,
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                if (id != null)
                  IconButton(
                    tooltip: 'Futa',
                    onPressed: () async {
                      final ok = await showDialog<bool>(
                            context: context,
                            builder: (_) => AlertDialog(
                              title: const Text('Futa bajeti?'),
                              content: Text('Unataka kufuta bajeti ya "$category"?'),
                              actions: [
                                TextButton(
                                  onPressed: () => Navigator.of(context).pop(false),
                                  child: const Text('Ghairi'),
                                ),
                                FilledButton(
                                  onPressed: () => Navigator.of(context).pop(true),
                                  child: const Text('Futa'),
                                ),
                              ],
                            ),
                          ) ??
                          false;

                      if (!ok) return;
                      // ignore: use_build_context_synchronously
                      await ProviderScope.containerOf(context).read(budgetsActionsProvider).delete(id);
                    },
                    icon: Icon(Icons.delete_outline, color: Colors.red.shade700),
                  ),
                FilledButton.tonalIcon(
                  onPressed: () => onEdit(limit),
                  icon: const Icon(Icons.edit_outlined),
                  label: const Text('Hariri'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _BudgetsHeader extends StatelessWidget {
  const _BudgetsHeader({
    required this.currency,
    required this.totalLimit,
    required this.totalSpent,
    required this.totalRemaining,
  });

  final String currency;
  final double totalLimit;
  final double totalSpent;
  final double totalRemaining;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

    final pct = totalLimit > 0 ? (totalSpent / totalLimit).clamp(0, 1).toDouble() : 0.0;
    final color = totalSpent > totalLimit && totalLimit > 0 ? Colors.red : primary;

    final donutColors = <Color>[primary, Colors.purple, color];

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: surface,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: outline),
        gradient: LinearGradient(
          colors: [
            primary.withValues(alpha: 0.10),
            Colors.purple.withValues(alpha: 0.08),
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
                  color: primary.withValues(alpha: 0.14),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(Icons.pie_chart, color: primary),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Muhtasari wa Bajeti', style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900)),
                    Text('Jumla ya mwezi huu', style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor)),
                  ],
                ),
              ),
              SizedBox(
                width: 66,
                height: 66,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    CustomPaint(
                      size: const Size(66, 66),
                      painter: _BudgetDonutPainter(
                        percent: pct,
                        trackColor: theme.colorScheme.outlineVariant.withValues(alpha: 0.45),
                        gradientColors: donutColors,
                      ),
                    ),
                    Text(
                      '${(pct * 100).toStringAsFixed(0)}%',
                      style: theme.textTheme.labelLarge?.copyWith(fontWeight: FontWeight.w900),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(child: _MiniStat(label: 'Limit', value: '$currency ${totalLimit.toStringAsFixed(0)}')),
              const SizedBox(width: 10),
              Expanded(child: _MiniStat(label: 'Spent', value: '$currency ${totalSpent.toStringAsFixed(0)}', valueColor: color)),
              const SizedBox(width: 10),
              Expanded(child: _MiniStat(label: 'Remain', value: '$currency ${totalRemaining.toStringAsFixed(0)}')),
            ],
          ),
          const SizedBox(height: 10),
          ClipRRect(
            borderRadius: BorderRadius.circular(999),
            child: LinearProgressIndicator(
              value: pct,
              minHeight: 10,
              backgroundColor: theme.colorScheme.outlineVariant.withValues(alpha: 0.45),
              color: color,
            ),
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
