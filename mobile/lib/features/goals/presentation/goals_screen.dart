import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/presentation/auth_controller.dart';
import 'goals_controller.dart';

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
        return ListView(
          padding: const EdgeInsets.all(12),
          children: [
            _GoalsHeader(
              onAdd: () => _openCreateSheet(context, ref, currency),
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
                      onAddProgress: () => _openProgressSheet(context, ref, currency, g),
                      onEdit: () => _openEditSheet(context, ref, currency, g),
                      onDelete: () => _confirmDelete(context, ref, g),
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
  });

  final String currency;
  final Map<String, dynamic> goal;
  final VoidCallback onAddProgress;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final title = (goal['title'] ?? '').toString();
    final desc = (goal['description'] ?? '').toString();

    final target = (goal['target_amount'] as num?)?.toDouble() ?? 0;
    final current = (goal['current_amount'] as num?)?.toDouble() ?? 0;
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
                Text('${(pct * 100).toStringAsFixed(0)}%'),
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
                IconButton(
                  tooltip: 'Ongeza progress',
                  onPressed: onAddProgress,
                  icon: Icon(Icons.add, color: Colors.green.shade700),
                ),
                IconButton(
                  tooltip: 'Hariri',
                  onPressed: onEdit,
                  icon: Icon(Icons.edit_outlined, color: theme.colorScheme.primary),
                ),
                IconButton(
                  tooltip: 'Futa',
                  onPressed: onDelete,
                  icon: Icon(Icons.delete_outline, color: Colors.red.shade700),
                ),
              ],
            ),
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
  final titleCtrl = TextEditingController();
  final targetCtrl = TextEditingController();
  final currentCtrl = TextEditingController();
  final descCtrl = TextEditingController();
  final dueCtrl = TextEditingController();

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
            const Text('Ongeza Lengo', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
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
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: targetCtrl,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(labelText: 'Target ($currency)', border: const OutlineInputBorder()),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: TextField(
                    controller: currentCtrl,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(labelText: 'Umefika ($currency)', border: const OutlineInputBorder()),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),
            TextField(
              controller: dueCtrl,
              decoration: const InputDecoration(
                labelText: 'Due date (YYYY-MM-DD) (hiari)',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 12),
            FilledButton(
              onPressed: () async {
                final title = titleCtrl.text.trim();
                final target = double.tryParse(targetCtrl.text.trim());
                final current = currentCtrl.text.trim().isEmpty ? null : double.tryParse(currentCtrl.text.trim());

                if (title.isEmpty || target == null) return;

                await ref.read(goalsActionsProvider).create(
                      title: title,
                      targetAmount: target,
                      currentAmount: current,
                      description: descCtrl.text.trim().isEmpty ? null : descCtrl.text.trim(),
                      dueDate: dueCtrl.text.trim().isEmpty ? null : dueCtrl.text.trim(),
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
}

Future<void> _openProgressSheet(BuildContext context, WidgetRef ref, String currency, Map<String, dynamic> goal) async {
  final id = (goal['id'] as num?)?.toInt() ?? 0;
  if (id <= 0) return;

  final current = (goal['current_amount'] as num?)?.toDouble() ?? 0;
  final target = (goal['target_amount'] as num?)?.toDouble() ?? 0;

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
  final targetCtrl = TextEditingController(text: ((goal['target_amount'] as num?)?.toDouble() ?? 0).toStringAsFixed(0));
  final descCtrl = TextEditingController(text: (goal['description'] ?? '').toString());
  final dueCtrl = TextEditingController(text: (goal['due_date'] ?? '').toString());

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
                  controller: dueCtrl,
                  decoration: const InputDecoration(labelText: 'Due date (YYYY-MM-DD) (hiari)', border: OutlineInputBorder()),
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
                          dueDate: dueCtrl.text.trim().isEmpty ? null : dueCtrl.text.trim(),
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
