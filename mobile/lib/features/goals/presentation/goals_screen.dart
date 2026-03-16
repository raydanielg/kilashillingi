import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/presentation/auth_controller.dart';
import 'goals_controller.dart';
import 'dart:async';
import 'goal_installments_screen.dart';

double _asDouble(dynamic v, [double fallback = 0]) {
  if (v == null) return fallback;
  if (v is num) return v.toDouble();
  final s = v.toString().trim();
  return double.tryParse(s) ?? fallback;
}

void _openInstallments(BuildContext context, Map<String, dynamic> goal, String currency) {
  final id = (goal['id'] as num?)?.toInt() ?? 0;
  if (id <= 0) return;
  final title = (goal['title'] ?? '').toString();

  Navigator.of(context).push(
    MaterialPageRoute(
      builder: (_) => GoalInstallmentsScreen(
        goalId: id,
        goalTitle: title,
        currency: currency,
      ),
    ),
  );
}

Future<void> _openAddInstallmentSheet(BuildContext context, WidgetRef ref, String currency, Map<String, dynamic> goal) async {
  final id = (goal['id'] as num?)?.toInt() ?? 0;
  if (id <= 0) return;

  final ctrl = TextEditingController();
  final noteCtrl = TextEditingController();

  final now = DateTime.now();
  final date = _fmtYmd(now);

  await showModalBottomSheet<void>(
    context: context,
    isScrollControlled: true,
    showDragHandle: true,
    builder: (context) {
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
            const Text('Ongeza Installment', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
            const SizedBox(height: 6),
            Text('Tarehe: $date', style: Theme.of(context).textTheme.labelMedium),
            const SizedBox(height: 12),
            TextField(
              controller: ctrl,
              keyboardType: TextInputType.number,
              decoration: InputDecoration(
                labelText: 'Kiasi ($currency)',
                border: const OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: noteCtrl,
              decoration: const InputDecoration(
                labelText: 'Maelezo (hiari)',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 12),
            FilledButton.icon(
              onPressed: () async {
                final amount = double.tryParse(ctrl.text.trim());
                if (amount == null || amount <= 0) return;
                await ref.read(goalsActionsProvider).addInstallment(
                      id,
                      amount: amount,
                      date: date,
                      note: noteCtrl.text.trim().isEmpty ? null : noteCtrl.text.trim(),
                    );
                if (context.mounted) Navigator.of(context).pop();
              },
              icon: const Icon(Icons.check_circle_outline),
              label: const Text('Hifadhi Installment'),
            ),
            const SizedBox(height: 10),
          ],
        ),
      );
    },
  );
}

class _OverallProgressDonut extends StatelessWidget {
  const _OverallProgressDonut({
    required this.percent,
    required this.currency,
    required this.totalCurrent,
    required this.totalTarget,
  });

  final double percent;
  final String currency;
  final double totalCurrent;
  final double totalTarget;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;
    final p = percent.clamp(0, 1).toDouble();

    final start = primary;
    final mid = Colors.purple;
    final end = Colors.orange;

    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(18),
        side: BorderSide(color: theme.dividerColor.withValues(alpha: 0.08)),
      ),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Row(
          children: [
            SizedBox(
              width: 110,
              height: 110,
              child: Stack(
                alignment: Alignment.center,
                children: [
                  CustomPaint(
                    size: const Size(110, 110),
                    painter: _DonutPainter(
                      percent: p,
                      trackColor: Colors.black12,
                      gradientColors: [start, mid, end],
                    ),
                  ),
                  Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        '${(p * 100).toStringAsFixed(0)}%',
                        style: theme.textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900),
                      ),
                      Text(
                        'jumla',
                        style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Progress ya Malengo Yote',
                    style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    '$currency ${totalCurrent.toStringAsFixed(0)} / $currency ${totalTarget.toStringAsFixed(0)}',
                    style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w800),
                  ),
                  const SizedBox(height: 6),
                  Container(
                    height: 10,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(999),
                      color: Colors.black12,
                    ),
                    child: FractionallySizedBox(
                      alignment: Alignment.centerLeft,
                      widthFactor: p,
                      child: DecoratedBox(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(999),
                          gradient: LinearGradient(colors: [start, mid, end]),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    'Huu ni wastani wa progress kulingana na target zote.',
                    style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _DonutPainter extends CustomPainter {
  _DonutPainter({
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
    final radius = (size.shortestSide / 2) - 6;
    const stroke = 12.0;

    final trackPaint = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = stroke
      ..strokeCap = StrokeCap.round
      ..color = trackColor;

    canvas.drawCircle(center, radius, trackPaint);

    if (percent <= 0) return;

    final rect = Rect.fromCircle(center: center, radius: radius);
    final sweep = 2 * 3.141592653589793 * percent.clamp(0, 1);

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

    canvas.drawArc(
      rect,
      -3.141592653589793 / 2,
      sweep,
      false,
      progressPaint,
    );
  }

  @override
  bool shouldRepaint(covariant _DonutPainter oldDelegate) {
    return oldDelegate.percent != percent || oldDelegate.trackColor != trackColor;
  }
}

String _two(int v) => v.toString().padLeft(2, '0');

String _formatCountdown(Duration d) {
  if (d.isNegative) return 'Muda umeisha';
  final days = d.inDays;
  final hours = d.inHours % 24;
  final mins = d.inMinutes % 60;
  final secs = d.inSeconds % 60;
  if (days > 0) {
    return '${days}d ${_two(hours)}:${_two(mins)}:${_two(secs)}';
  }
  return '${_two(hours)}:${_two(mins)}:${_two(secs)}';
}

String _fmtYmd(DateTime d) {
  return '${d.year.toString().padLeft(4, '0')}-${d.month.toString().padLeft(2, '0')}-${d.day.toString().padLeft(2, '0')}';
}

class GoalsScreen extends ConsumerWidget {
  const GoalsScreen({super.key, this.embedded = false});

  final bool embedded;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final theme = Theme.of(context);
    final auth = ref.watch(authStateProvider);
    final currency = (auth.user?['currency'] ?? 'KSh').toString();

    final asyncGoals = ref.watch(goalsListProvider);

    final body = asyncGoals.when(
      data: (goals) {
        final totalTarget = goals.fold<double>(0.0, (sum, g) => sum + _asDouble(g['target_amount']));
        final totalCurrent = goals.fold<double>(0.0, (sum, g) => sum + _asDouble(g['current_amount']));
        final overallPct = totalTarget > 0 ? (totalCurrent / totalTarget).clamp(0, 1).toDouble() : 0.0;

        return ListView(
          padding: const EdgeInsets.all(12),
          children: [
            _GoalsHeader(
              onAdd: () => _openCreateSheet(context, ref, currency),
            ),
            const SizedBox(height: 12),
            _OverallProgressDonut(
              percent: overallPct,
              currency: currency,
              totalCurrent: totalCurrent,
              totalTarget: totalTarget,
            ),
            const SizedBox(height: 12),
            SizedBox(
              height: 52,
              child: FilledButton.icon(
                onPressed: () => _openCreateSheet(context, ref, currency),
                icon: const Icon(Icons.add),
                label: const Text('Ongeza Lengo'),
              ),
            ),
            const SizedBox(height: 12),
            if (goals.isEmpty)
              Padding(
                padding: const EdgeInsets.only(top: 20),
                child: Text(
                  'Bado hujatengeneza lengo. Bonyeza "+" kuanza.',
                  textAlign: TextAlign.center,
                  style: theme.textTheme.bodyMedium?.copyWith(color: theme.hintColor),
                ),
              )
            else
              ...goals.map((g) => Padding(
                    padding: const EdgeInsets.only(bottom: 10),
                    child: _GoalCard(
                      currency: currency,
                      goal: g,
                      onAddProgress: () => _openAddInstallmentSheet(context, ref, currency, g),
                      onEdit: () => _openEditSheet(context, ref, currency, g),
                      onDelete: () => _confirmDelete(context, ref, g),
                      onViewInstallments: () => _openInstallments(context, g, currency),
                    ),
                  )),
          ],
        );
      },
      loading: () => const Center(child: CircularProgressIndicator()),
      error: (e, _) => Center(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text('Imeshindikana kupakia malengo:\n$e', textAlign: TextAlign.center),
              const SizedBox(height: 12),
              FilledButton(
                onPressed: () => ref.read(goalsActionsProvider).refresh(),
                child: const Text('Jaribu tena'),
              ),
            ],
          ),
        ),
      ),
    );

    if (embedded) return body;

    return Scaffold(
      appBar: AppBar(title: const Text('Malengo')),
      body: SafeArea(child: body),
      floatingActionButton: FloatingActionButton(
        heroTag: 'goals_fab',
        onPressed: () => _openCreateSheet(context, ref, currency),
        child: const Icon(Icons.add),
      ),
    );
  }
}

class _GoalsHeader extends StatelessWidget {
  const _GoalsHeader({required this.onAdd});

  final VoidCallback onAdd;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(18),
        gradient: LinearGradient(
          colors: [
            primary.withValues(alpha: 0.12),
            Colors.red.withValues(alpha: 0.10),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        border: Border.all(color: primary.withValues(alpha: 0.15)),
      ),
      child: Row(
        children: [
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              color: primary.withValues(alpha: 0.14),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: primary.withValues(alpha: 0.22)),
            ),
            child: Icon(Icons.track_changes, color: primary),
          ),
          const SizedBox(width: 12),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Malengo', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                SizedBox(height: 2),
                Text('Tengeneza target, fuatilia progress na deadline.'),
              ],
            ),
          ),
          IconButton(
            tooltip: 'Ongeza lengo',
            onPressed: onAdd,
            icon: const Icon(Icons.add_circle_outline),
          ),
        ],
      ),
    );
  }
}

class _GoalCard extends StatelessWidget {
  const _GoalCard({
    required this.currency,
    required this.goal,
    required this.onAddProgress,
    required this.onEdit,
    required this.onDelete,
    required this.onViewInstallments,
  });

  final String currency;
  final Map<String, dynamic> goal;
  final VoidCallback onAddProgress;
  final VoidCallback onEdit;
  final VoidCallback onDelete;
  final VoidCallback onViewInstallments;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final title = (goal['title'] ?? '').toString();
    final desc = (goal['description'] ?? '').toString();

    final target = _asDouble(goal['target_amount']);
    final current = _asDouble(goal['current_amount']);
    final status = (goal['status'] ?? 'active').toString();

    final due = (goal['due_date'] ?? '').toString();
    final pct = target > 0 ? (current / target).clamp(0, 1).toDouble() : 0.0;

    final isDone = status == 'completed' || (target > 0 && current >= target);
    final isPaused = status == 'paused';

    final Color color;
    if (isDone) {
      color = Colors.green;
    } else if (isPaused) {
      color = Colors.blueGrey;
    } else {
      color = theme.colorScheme.primary;
    }

    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(18),
        side: BorderSide(color: theme.dividerColor.withValues(alpha: 0.08)),
      ),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    title.isEmpty ? 'Lengo' : title,
                    style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                _StatusChip(status: status, isDone: isDone),
              ],
            ),
            if (desc.trim().isNotEmpty) ...[
              const SizedBox(height: 6),
              Text(desc, style: theme.textTheme.bodySmall?.copyWith(color: theme.hintColor)),
            ],
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: Text(
                    '$currency ${current.toStringAsFixed(0)} / $currency ${target.toStringAsFixed(0)}',
                    style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w800),
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: 0.10),
                    borderRadius: BorderRadius.circular(999),
                  ),
                  child: Text(
                    '${(pct * 100).toStringAsFixed(0)}%',
                    style: TextStyle(fontWeight: FontWeight.w900, color: color, fontSize: 12),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            ClipRRect(
              borderRadius: BorderRadius.circular(999),
              child: LinearProgressIndicator(
                minHeight: 10,
                value: pct,
                backgroundColor: Colors.black12,
                color: color,
              ),
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                Expanded(
                  child: Text(
                    due.trim().isEmpty ? 'Mwisho: -' : 'Mwisho: $due',
                    style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor),
                  ),
                ),
                _MiniPillButton(
                  label: 'Ongeza',
                  color: Colors.green,
                  icon: Icons.savings_outlined,
                  onTap: onAddProgress,
                ),
                const SizedBox(width: 6),
                _MiniIconButton(
                  tooltip: 'Historia ya installments',
                  onTap: onViewInstallments,
                  icon: Icons.chevron_right,
                  color: theme.colorScheme.primary,
                ),
                const SizedBox(width: 2),
                _MiniIconButton(
                  tooltip: 'Hariri',
                  onTap: onEdit,
                  icon: Icons.edit_outlined,
                  color: theme.colorScheme.primary,
                ),
                _MiniIconButton(
                  tooltip: 'Futa',
                  onTap: onDelete,
                  icon: Icons.delete_outline,
                  color: Colors.red.shade700,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _MiniIconButton extends StatelessWidget {
  const _MiniIconButton({
    required this.tooltip,
    required this.onTap,
    required this.icon,
    required this.color,
  });

  final String tooltip;
  final VoidCallback onTap;
  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Padding(
        padding: const EdgeInsets.all(8),
        child: Icon(icon, color: color, size: 20),
      ),
    );
  }
}

class _MiniPillButton extends StatelessWidget {
  const _MiniPillButton({
    required this.label,
    required this.color,
    required this.icon,
    required this.onTap,
  });

  final String label;
  final Color color;
  final IconData icon;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(999),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.12),
          borderRadius: BorderRadius.circular(999),
          border: Border.all(color: color.withValues(alpha: 0.22)),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, color: color, size: 16),
            const SizedBox(width: 6),
            Text(label, style: TextStyle(color: color, fontWeight: FontWeight.w900, fontSize: 12)),
          ],
        ),
      ),
    );
  }
}

class _StatusChip extends StatelessWidget {
  const _StatusChip({required this.status, required this.isDone});

  final String status;
  final bool isDone;

  @override
  Widget build(BuildContext context) {
    final s = status.toLowerCase();

    final Color bg;
    final Color fg;
    final String label;

    if (isDone || s == 'completed') {
      bg = Colors.green.withValues(alpha: 0.12);
      fg = Colors.green.shade800;
      label = 'Done';
    } else if (s == 'paused') {
      bg = Colors.blueGrey.withValues(alpha: 0.12);
      fg = Colors.blueGrey.shade800;
      label = 'Paused';
    } else {
      bg = Colors.indigo.withValues(alpha: 0.12);
      fg = Colors.indigo.shade800;
      label = 'Active';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: fg,
          fontWeight: FontWeight.w900,
          fontSize: 11,
        ),
      ),
    );
  }
}

Future<void> _confirmDelete(BuildContext context, WidgetRef ref, Map<String, dynamic> goal) async {
  final id = (goal['id'] as num?)?.toInt() ?? 0;
  if (id <= 0) return;

  final ok = await showDialog<bool>(
        context: context,
        builder: (_) => AlertDialog(
          title: const Text('Futa Lengo?'),
          content: const Text('Uko tayari kufuta lengo hili?'),
          actions: [
            TextButton(onPressed: () => Navigator.of(context).pop(false), child: const Text('Ghairi')),
            FilledButton(onPressed: () => Navigator.of(context).pop(true), child: const Text('Futa')),
          ],
        ),
      ) ??
      false;

  if (!ok) return;
  await ref.read(goalsActionsProvider).delete(id);
}

Future<void> _openCreateSheet(BuildContext context, WidgetRef ref, String currency) async {
  await showModalBottomSheet<void>(
    context: context,
    isScrollControlled: true,
    showDragHandle: true,
    builder: (context) {
      return _CreateGoalSheet(currency: currency);
    },
  );
}

class _CreateGoalSheet extends ConsumerStatefulWidget {
  const _CreateGoalSheet({required this.currency});

  final String currency;

  @override
  ConsumerState<_CreateGoalSheet> createState() => _CreateGoalSheetState();
}

class _CreateGoalSheetState extends ConsumerState<_CreateGoalSheet> {
  final _titleCtrl = TextEditingController();
  final _targetCtrl = TextEditingController();

  late final DateTime _start;
  DateTime? _due;
  Timer? _timer;
  Duration _remaining = Duration.zero;

  @override
  void initState() {
    super.initState();
    _start = DateTime.now();
    _due = null;
    _remaining = Duration.zero;
    _timer = Timer.periodic(const Duration(seconds: 1), (_) {
      if (!mounted) return;
      setState(() {
        final due = _due;
        _remaining = due == null ? Duration.zero : due.difference(DateTime.now());
      });
    });
  }

  void _setDue(DateTime picked) {
    setState(() {
      _due = DateTime(picked.year, picked.month, picked.day, 23, 59, 59);
      _remaining = _due!.difference(DateTime.now());
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _titleCtrl.dispose();
    _targetCtrl.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    final title = _titleCtrl.text.trim();
    final target = double.tryParse(_targetCtrl.text.trim());
    final due = _due;
    if (title.isEmpty || target == null) return;
    if (due == null) return;

    await ref.read(goalsActionsProvider).create(
          title: title,
          targetAmount: target,
          currentAmount: 0,
          startDate: _fmtYmd(_start),
          dueDate: _fmtYmd(due),
        );

    if (mounted) Navigator.of(context).pop();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;
    final due = _due;

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
                child: Icon(Icons.track_changes, color: primary),
              ),
              const SizedBox(width: 12),
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Ongeza Lengo', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
                    SizedBox(height: 2),
                    Text('Jaza jina na target tu. Deadline inawekwa auto.'),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(14),
              color: primary.withValues(alpha: 0.08),
              border: Border.all(color: primary.withValues(alpha: 0.15)),
            ),
            child: Row(
              children: [
                Icon(Icons.timer_outlined, color: primary, size: 18),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    due == null
                        ? 'Chagua due date ili uanze countdown'
                        : 'Muda uliobaki: ${_formatCountdown(_remaining)}',
                    style: const TextStyle(fontWeight: FontWeight.w800),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 10),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: [
              _QuickDueChip(
                label: '+7 days',
                onTap: () => _setDue(DateTime.now().add(const Duration(days: 7))),
              ),
              _QuickDueChip(
                label: '+30 days',
                onTap: () => _setDue(DateTime.now().add(const Duration(days: 30))),
              ),
              _QuickDueChip(
                label: '+90 days',
                onTap: () => _setDue(DateTime.now().add(const Duration(days: 90))),
              ),
            ],
          ),
          const SizedBox(height: 10),
          InkWell(
            onTap: () async {
              final picked = await showDatePicker(
                context: context,
                initialDate: due ?? DateTime.now().add(const Duration(days: 30)),
                firstDate: DateTime.now(),
                lastDate: DateTime.now().add(const Duration(days: 3650)),
              );
              if (picked == null) return;
              _setDue(picked);
            },
            borderRadius: BorderRadius.circular(14),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(14),
                color: Colors.white,
                border: Border.all(color: primary.withValues(alpha: 0.25)),
              ),
              child: Row(
                children: [
                  Icon(Icons.event, color: primary, size: 18),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      due == null ? 'Chagua Due Date (lazima)' : 'Due Date: ${_fmtYmd(due)}',
                      style: const TextStyle(fontWeight: FontWeight.w900),
                    ),
                  ),
                  Icon(Icons.chevron_right, color: primary.withValues(alpha: 0.9)),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _titleCtrl,
            textInputAction: TextInputAction.next,
            decoration: const InputDecoration(
              labelText: 'Jina la lengo',
              border: OutlineInputBorder(),
            ),
          ),
          const SizedBox(height: 10),
          TextField(
            controller: _targetCtrl,
            keyboardType: TextInputType.number,
            onSubmitted: (_) => _save(),
            decoration: InputDecoration(
              labelText: 'Target (${widget.currency})',
              border: const OutlineInputBorder(),
            ),
          ),
          const SizedBox(height: 12),
          FilledButton(
            onPressed: due == null ? null : _save,
            child: const Text('Hifadhi'),
          ),
          const SizedBox(height: 10),
        ],
      ),
    );
  }
}

class _QuickDueChip extends StatelessWidget {
  const _QuickDueChip({required this.label, required this.onTap});

  final String label;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(999),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        decoration: BoxDecoration(
          color: primary.withValues(alpha: 0.10),
          borderRadius: BorderRadius.circular(999),
          border: Border.all(color: primary.withValues(alpha: 0.18)),
        ),
        child: Text(
          label,
          style: TextStyle(
            fontWeight: FontWeight.w900,
            color: primary,
          ),
        ),
      ),
    );
  }
}

Future<void> _openProgressSheet(BuildContext context, WidgetRef ref, String currency, Map<String, dynamic> goal) async {
  final id = (goal['id'] as num?)?.toInt() ?? 0;
  if (id <= 0) return;

  final current = _asDouble(goal['current_amount']);
  final target = _asDouble(goal['target_amount']);

  final ctrl = TextEditingController(text: current.toStringAsFixed(0));

  await showModalBottomSheet<void>(
    context: context,
    isScrollControlled: true,
    showDragHandle: true,
    builder: (context) {
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
            Text('Ongeza Progress', style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900)),
            const SizedBox(height: 8),
            Text('Target: $currency ${target.toStringAsFixed(0)}', style: Theme.of(context).textTheme.bodySmall),
            const SizedBox(height: 10),
            TextField(
              controller: ctrl,
              keyboardType: TextInputType.number,
              decoration: InputDecoration(labelText: 'Umefika ($currency)', border: const OutlineInputBorder()),
            ),
            const SizedBox(height: 12),
            FilledButton(
              onPressed: () async {
                final v = double.tryParse(ctrl.text.trim());
                if (v == null) return;
                await ref.read(goalsActionsProvider).update(id, currentAmount: v);
                if (context.mounted) Navigator.of(context).pop();
              },
              child: const Text('Hifadhi'),
            ),
            const SizedBox(height: 10),
          ],
        ),
      );
    },
  );
}

Future<void> _openEditSheet(BuildContext context, WidgetRef ref, String currency, Map<String, dynamic> goal) async {
  final id = (goal['id'] as num?)?.toInt() ?? 0;
  if (id <= 0) return;

  final titleCtrl = TextEditingController(text: (goal['title'] ?? '').toString());
  final targetCtrl = TextEditingController(text: _asDouble(goal['target_amount']).toStringAsFixed(0));
  final descCtrl = TextEditingController(text: (goal['description'] ?? '').toString());
  DateTime? due;
  final dueRaw = (goal['due_date'] ?? '').toString().trim();
  if (dueRaw.isNotEmpty) {
    final parsed = DateTime.tryParse(dueRaw);
    if (parsed != null) {
      due = DateTime(parsed.year, parsed.month, parsed.day, 23, 59, 59);
    }
  }

  String status = (goal['status'] ?? 'active').toString();

  await showModalBottomSheet<void>(
    context: context,
    isScrollControlled: true,
    showDragHandle: true,
    builder: (context) {
      return StatefulBuilder(
        builder: (context, setState) {
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
                const Text('Hariri Lengo', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
                const SizedBox(height: 12),
                TextField(
                  controller: titleCtrl,
                  decoration: const InputDecoration(labelText: 'Jina la lengo', border: OutlineInputBorder()),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: descCtrl,
                  maxLines: 3,
                  decoration: const InputDecoration(labelText: 'Maelezo (hiari)', border: OutlineInputBorder()),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: targetCtrl,
                  keyboardType: TextInputType.number,
                  decoration: InputDecoration(labelText: 'Target ($currency)', border: const OutlineInputBorder()),
                ),
                const SizedBox(height: 10),
                TextField(
                  readOnly: true,
                  onTap: () async {
                    final picked = await showDatePicker(
                      context: context,
                      initialDate: due ?? DateTime.now().add(const Duration(days: 30)),
                      firstDate: DateTime.now(),
                      lastDate: DateTime.now().add(const Duration(days: 3650)),
                    );
                    if (picked == null) return;
                    setState(() {
                      due = DateTime(picked.year, picked.month, picked.day, 23, 59, 59);
                    });
                  },
                  decoration: InputDecoration(
                    labelText: 'Due date (ongeza/extend)',
                    border: const OutlineInputBorder(),
                    hintText: 'Chagua tarehe ya mwisho',
                    suffixIcon: const Icon(Icons.event),
                  ),
                  controller: TextEditingController(text: due == null ? '' : _fmtYmd(due!)),
                ),
                const SizedBox(height: 10),
                DropdownButtonFormField<String>(
                  value: status,
                  decoration: const InputDecoration(labelText: 'Hali', border: OutlineInputBorder()),
                  items: const [
                    DropdownMenuItem(value: 'active', child: Text('Active')),
                    DropdownMenuItem(value: 'paused', child: Text('Paused')),
                    DropdownMenuItem(value: 'completed', child: Text('Completed')),
                  ],
                  onChanged: (v) {
                    if (v == null) return;
                    setState(() => status = v);
                  },
                ),
                const SizedBox(height: 12),
                FilledButton(
                  onPressed: () async {
                    final title = titleCtrl.text.trim();
                    final target = double.tryParse(targetCtrl.text.trim());
                    if (title.isEmpty || target == null) return;

                    await ref.read(goalsActionsProvider).update(
                          id,
                          title: title,
                          description: descCtrl.text.trim(),
                          targetAmount: target,
                          dueDate: due == null ? null : _fmtYmd(due!),
                          status: status,
                        );

                    if (context.mounted) Navigator.of(context).pop();
                  },
                  child: const Text('Hifadhi'),
                ),
                const SizedBox(height: 10),
              ],
            ),
          );
        },
      );
    },
  );
}
