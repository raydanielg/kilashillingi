import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../auth/presentation/auth_state.dart';
import '../../budgets/presentation/budgets_screen.dart';
import '../../goals/presentation/goals_screen.dart';
import '../../reports/presentation/reports_screen.dart';
import '../../profile/presentation/profile_screen.dart';
import '../../transactions/presentation/transactions_screen.dart';
import '../../transactions/presentation/transactions_controller.dart';
import '../../../core/config/api_config.dart';
import '../../../app/app.dart';
import 'dashboard_screen.dart';
import 'header.dart';

class _QuickAddGrid extends StatelessWidget {
  const _QuickAddGrid({required this.onTap});
  final Function(String type) onTap;

  @override
  Widget build(BuildContext context) {
    return GridView.count(
      shrinkWrap: true,
      crossAxisCount: 2,
      mainAxisSpacing: 12,
      crossAxisSpacing: 12,
      childAspectRatio: 1.4,
      physics: const NeverScrollableScrollPhysics(),
      children: [
        _QuickAddItem(
          label: 'Mapato',
          icon: Icons.add_chart,
          color: Colors.green,
          onTap: () => onTap('income'),
        ),
        _QuickAddItem(
          label: 'Matumizi',
          icon: Icons.payments_outlined,
          color: Colors.red,
          onTap: () => onTap('expense'),
        ),
        _QuickAddItem(
          label: 'Bajeti',
          icon: Icons.pie_chart_outline,
          color: Colors.blue,
          onTap: () => onTap('budget'),
        ),
        _QuickAddItem(
          label: 'Deni',
          icon: Icons.history_outlined,
          color: Colors.orange,
          onTap: () => onTap('debt'),
        ),
      ],
    );
  }
}

class _FabHorizontalMenu extends StatelessWidget {
  const _FabHorizontalMenu({
    required this.onIncome,
    required this.onExpense,
    required this.onBudget,
    required this.onGoals,
  });

  final VoidCallback onIncome;
  final VoidCallback onExpense;
  final VoidCallback onBudget;
  final VoidCallback onGoals;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

    return Container(
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: surface,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: outline),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.12),
            blurRadius: 18,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          _FabMenuItem(
            label: 'Mapato',
            icon: Icons.add_chart,
            color: Colors.green,
            onTap: onIncome,
          ),
          const SizedBox(width: 8),
          _FabMenuItem(
            label: 'Matumizi',
            icon: Icons.payments_outlined,
            color: Colors.red,
            onTap: onExpense,
          ),
          const SizedBox(width: 8),
          _FabMenuItem(
            label: 'Bajeti',
            icon: Icons.pie_chart_outline,
            color: Colors.orange,
            onTap: onBudget,
          ),
          const SizedBox(width: 8),
          _FabMenuItem(
            label: 'Malengo',
            icon: Icons.track_changes_outlined,
            color: Colors.indigo,
            onTap: onGoals,
          ),
        ],
      ),
    );
  }
}

class _FabMenuItem extends StatelessWidget {
  const _FabMenuItem({
    required this.label,
    required this.icon,
    required this.color,
    required this.onTap,
  });

  final String label;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        width: 72,
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 10),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.10),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: color.withValues(alpha: 0.18)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, color: color, size: 22),
            const SizedBox(height: 6),
            Text(
              label,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: theme.textTheme.labelSmall?.copyWith(fontWeight: FontWeight.w900, color: theme.colorScheme.onSurface),
            ),
          ],
        ),
      ),
    );
  }
}

class _QuickAddItem extends StatelessWidget {
  const _QuickAddItem({
    required this.label,
    required this.icon,
    required this.color,
    required this.onTap,
  });

  final String label;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.1),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: color.withValues(alpha: 0.2)),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: color, size: 28),
            const SizedBox(height: 8),
            Text(
              label,
              style: TextStyle(
                color: color,
                fontWeight: FontWeight.w800,
                fontSize: 14,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _UserAvatar extends StatelessWidget {
  const _UserAvatar({
    required this.avatarUrl,
    required this.initials,
    required this.onTap,
  });

  final String avatarUrl;
  final String initials;
  final VoidCallback onTap;

  String _getEffectiveUrl() {
    if (avatarUrl.isEmpty) return '';
    if (avatarUrl.startsWith('http')) return avatarUrl;
    
    final base = ApiConfig.baseUrl.replaceAll('/api', '');
    
    // Ensure path starts with a single slash
    var cleanPath = avatarUrl;
    if (!cleanPath.startsWith('/')) {
      cleanPath = '/$cleanPath';
    }
    
    // Check if path already includes storage
    if (cleanPath.startsWith('/storage/')) {
      return '$base$cleanPath';
    }
    
    // Most Laravel avatars are stored in storage/app/public/avatars/...
    // which is accessed via public/storage/avatars/...
    // If the database has 'avatars/3_1773116814.jpeg', we need '/storage/avatars/3_1773116814.jpeg'
    return '$base/storage$cleanPath';
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;
    final effectiveUrl = _getEffectiveUrl();

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(999),
      child: Container(
        padding: const EdgeInsets.all(2),
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          border: Border.all(color: primary.withValues(alpha: 0.2), width: 1.5),
        ),
        child: CircleAvatar(
          radius: 18,
          backgroundColor: primary.withValues(alpha: 0.1),
          backgroundImage: effectiveUrl.isNotEmpty ? NetworkImage(effectiveUrl) : null,
          child: effectiveUrl.isNotEmpty
              ? null
              : Text(
                  initials,
                  style: TextStyle(
                    fontWeight: FontWeight.w900,
                    fontSize: 12,
                    color: primary,
                  ),
                ),
        ),
      ),
    );
  }
}

class HomeTabs extends ConsumerStatefulWidget {
  const HomeTabs({super.key});

  @override
  ConsumerState<HomeTabs> createState() => _HomeTabsState();
}

class _HomeTabsState extends ConsumerState<HomeTabs> {
  int _index = 0;
  bool _fabOpen = false;

  void _goTo(int index) {
    if (_index == index) return;
    setState(() => _index = index);
  }

  void _toggleFabMenu() {
    setState(() => _fabOpen = !_fabOpen);
  }

  void _closeFabMenu() {
    if (!_fabOpen) return;
    setState(() => _fabOpen = false);
  }

  Future<void> _openQuickAddSheet() async {
    final theme = Theme.of(context);

    await showModalBottomSheet<void>(
      context: context,
      showDragHandle: true,
      isScrollControlled: true,
      builder: (context) {
        return SafeArea(
          child: Padding(
            padding: EdgeInsets.only(
              left: 16,
              right: 16,
              top: 8,
              bottom: 16 + MediaQuery.of(context).viewInsets.bottom,
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Text(
                  'Ongeza',
                  style: theme.textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900),
                ),
                const SizedBox(height: 16),
                _QuickAddGrid(
                  onTap: (type) async {
                    Navigator.of(context).pop();
                    if (type == 'income') {
                      _goTo(1);
                      await Future<void>.delayed(const Duration(milliseconds: 250));
                      await _openQuickTransactionDialog(type: 'income');
                    } else if (type == 'expense') {
                      _goTo(2);
                      await Future<void>.delayed(const Duration(milliseconds: 250));
                      await _openQuickTransactionDialog(type: 'expense');
                    } else if (type == 'budget') {
                      _goTo(3);
                    }
                  },
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Future<void> _openQuickTransactionDialog({required String type}) async {
    final amountCtrl = TextEditingController();
    final descCtrl = TextEditingController();

    final now = DateTime.now();
    final date = '${now.year.toString().padLeft(4, '0')}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')}';

    await showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      showDragHandle: true,
      builder: (context) {
        final theme = Theme.of(context);
        final primary = theme.colorScheme.primary;
        final isIncome = type == 'income';
        final color = isIncome ? Colors.green : Colors.red;

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
                      color: color.withValues(alpha: 0.12),
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: color.withValues(alpha: 0.22)),
                    ),
                    child: Icon(isIncome ? Icons.add_chart : Icons.payments_outlined, color: color),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          isIncome ? 'Ongeza Mapato' : 'Ongeza Matumizi',
                          style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900),
                        ),
                        Text(
                          'Tarehe: $date',
                          style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor),
                        ),
                      ],
                    ),
                  ),
                  Icon(Icons.lock_clock_outlined, color: primary.withValues(alpha: 0.7)),
                ],
              ),
              const SizedBox(height: 12),
              TextField(
                controller: amountCtrl,
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                  border: const OutlineInputBorder(),
                  labelText: 'Kiasi',
                  prefixIcon: Icon(Icons.attach_money, color: color),
                ),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: descCtrl,
                decoration: const InputDecoration(
                  border: OutlineInputBorder(),
                  labelText: 'Maelezo (hiari)',
                  prefixIcon: Icon(Icons.notes_outlined),
                ),
              ),
              const SizedBox(height: 12),
              FilledButton.icon(
                onPressed: () async {
                  final amount = double.tryParse(amountCtrl.text.trim());
                  if (amount == null) return;

                  await ref.read(transactionsActionsProvider).create(
                        type: type,
                        amount: amount,
                        date: date,
                        description: descCtrl.text.trim().isEmpty ? null : descCtrl.text.trim(),
                      );

                  if (context.mounted) Navigator.of(context).pop();
                },
                icon: const Icon(Icons.check_circle_outline),
                label: Text(isIncome ? 'Hifadhi Mapato' : 'Hifadhi Matumizi'),
                style: FilledButton.styleFrom(backgroundColor: color),
              ),
              const SizedBox(height: 10),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authStateProvider);

    if (auth.status != AuthStatus.authenticated) {
      return const DashboardScreen(embedded: false);
    }

    final name = (auth.user?['name'] ?? '').toString();
    final currency = (auth.user?['currency'] ?? 'KSh').toString();

    final titles = <String>[
      'Home',
      'Mapato',
      'Matumizi',
      'Bajeti',
      'Malengo',
      'Ripoti',
    ];

    final subtitles = <String>[
      name.isEmpty ? 'Karibu' : 'Karibu, $name',
      'Orodha ya mapato',
      'Orodha ya matumizi',
      'Mipango ya matumizi',
      'Malengo na progress',
      'Mchanganuo wa miamala',
    ];

    final themeMode = ref.watch(themeModeProvider);
    final isDark = themeMode == ThemeMode.dark;

    final avatarUrl = (auth.user?['avatar_url'] ?? auth.user?['avatar'] ?? '').toString();
    final initials = name.trim().isEmpty
        ? 'U'
        : name
            .trim()
            .split(RegExp(r'\s+'))
            .take(2)
            .map((p) => p.isEmpty ? '' : p[0].toUpperCase())
            .join();

    final pages = <Widget>[
      DashboardScreen(embedded: true),
      const TransactionsScreen(embedded: true, typeFilter: 'income'),
      const TransactionsScreen(embedded: true, typeFilter: 'expense'),
      const BudgetsScreen(embedded: true),
      const GoalsScreen(embedded: true),
      const ReportsScreen(embedded: true),
    ];

    final body = IndexedStack(
      index: _index,
      children: pages,
    );

    return Scaffold(
      body: Stack(
        children: [
          SafeArea(
            child: Column(
              children: [
                Header(
                  title: titles[_index],
                  subtitle: subtitles[_index],
                  trailing: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      IconButton(
                        tooltip: isDark ? 'Badili kuwa light mode' : 'Badili kuwa dark mode',
                        onPressed: () {
                          ref.read(themeModeProvider.notifier).state = isDark ? ThemeMode.light : ThemeMode.dark;
                        },
                        icon: Icon(isDark ? Icons.light_mode_outlined : Icons.dark_mode_outlined),
                      ),
                      const SizedBox(width: 4),
                      _UserAvatar(
                        avatarUrl: avatarUrl,
                        initials: initials,
                        onTap: () => Navigator.of(context).push(
                          MaterialPageRoute(builder: (_) => const ProfileScreen(embedded: false)),
                        ),
                      ),
                    ],
                  ),
                ),
                const Divider(height: 1),
                Expanded(
                  child: body,
                ),
              ],
            ),
          ),
          if (_fabOpen)
            Positioned.fill(
              child: GestureDetector(
                onTap: _closeFabMenu,
                child: Container(color: Colors.black.withValues(alpha: 0.25)),
              ),
            ),
          if (_index != 3)
            Positioned(
              right: 16,
              bottom: 16,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  AnimatedOpacity(
                    opacity: _fabOpen ? 1 : 0,
                    duration: const Duration(milliseconds: 160),
                    child: IgnorePointer(
                      ignoring: !_fabOpen,
                      child: AnimatedScale(
                        scale: _fabOpen ? 1 : 0.95,
                        duration: const Duration(milliseconds: 160),
                        child: _FabHorizontalMenu(
                          onIncome: () async {
                            _closeFabMenu();
                            _goTo(1);
                            await Future<void>.delayed(const Duration(milliseconds: 250));
                            await _openQuickTransactionDialog(type: 'income');
                          },
                          onExpense: () async {
                            _closeFabMenu();
                            _goTo(2);
                            await Future<void>.delayed(const Duration(milliseconds: 250));
                            await _openQuickTransactionDialog(type: 'expense');
                          },
                          onBudget: () {
                            _closeFabMenu();
                            _goTo(3);
                          },
                          onGoals: () {
                            _closeFabMenu();
                            _goTo(4);
                          },
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 10),
                  FloatingActionButton(
                    onPressed: _toggleFabMenu,
                    heroTag: 'home_tabs_fab',
                    child: Icon(_fabOpen ? Icons.close : Icons.add),
                  ),
                ],
              ),
            ),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: _goTo,
        labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home_rounded, color: Colors.blue),
            label: 'Home',
          ),
          NavigationDestination(
            icon: Icon(Icons.add_circle_outline),
            selectedIcon: Icon(Icons.add_circle, color: Colors.green),
            label: 'Mapato',
          ),
          NavigationDestination(
            icon: Icon(Icons.remove_circle_outline),
            selectedIcon: Icon(Icons.remove_circle, color: Colors.red),
            label: 'Matumizi',
          ),
          NavigationDestination(
            icon: Icon(Icons.pie_chart_outline),
            selectedIcon: Icon(Icons.pie_chart_rounded, color: Colors.orange),
            label: 'Bajeti',
          ),
          NavigationDestination(
            icon: Icon(Icons.track_changes_outlined),
            selectedIcon: Icon(Icons.track_changes, color: Colors.indigo),
            label: 'Malengo',
          ),
          NavigationDestination(
            icon: Icon(Icons.bar_chart_outlined),
            selectedIcon: Icon(Icons.bar_chart_rounded, color: Colors.purple),
            label: 'Ripoti',
          ),
        ],
      ),
    );
  }
}
