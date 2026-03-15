import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../auth/presentation/auth_state.dart';
import '../../budgets/presentation/budgets_screen.dart';
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
    // Handle relative paths from Laravel storage
    // If it starts with /storage, we just need the base URL
    // If it doesn't, we might need to add /storage/
    final base = ApiConfig.baseUrl.replaceAll('/api', '');
    if (avatarUrl.contains('storage/')) {
      final cleanPath = avatarUrl.startsWith('/') ? avatarUrl : '/$avatarUrl';
      return '$base$cleanPath';
    }
    final cleanPath = avatarUrl.startsWith('/') ? avatarUrl : '/$avatarUrl';
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

  void _goTo(int index) {
    if (_index == index) return;
    setState(() => _index = index);
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

    final ok = await showDialog<bool>(
          context: context,
          builder: (context) {
            return AlertDialog(
              title: Text(type == 'income' ? 'Ongeza Mapato' : 'Ongeza Matumizi'),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  TextField(
                    controller: amountCtrl,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(border: OutlineInputBorder(), labelText: 'Kiasi'),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: descCtrl,
                    decoration: const InputDecoration(border: OutlineInputBorder(), labelText: 'Maelezo (si lazima)'),
                  ),
                  const SizedBox(height: 8),
                  Text('Tarehe: $date'),
                ],
              ),
              actions: [
                TextButton(onPressed: () => Navigator.of(context).pop(false), child: const Text('Ghairi')),
                FilledButton(onPressed: () => Navigator.of(context).pop(true), child: const Text('Hifadhi')),
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
    ];

    final subtitles = <String>[
      name.isEmpty ? 'Karibu' : 'Karibu, $name',
      'Orodha ya mapato',
      'Orodha ya matumizi',
      'Mipango ya matumizi',
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
    ];

    final body = IndexedStack(
      index: _index,
      children: pages,
    );

    return Scaffold(
      body: SafeArea(
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
      floatingActionButton: _index == 3
          ? null
          : FloatingActionButton(
              onPressed: _openQuickAddSheet,
              heroTag: 'home_tabs_fab',
              child: const Icon(Icons.add),
            ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: _goTo,
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home_outlined), selectedIcon: Icon(Icons.home), label: 'Home'),
          NavigationDestination(icon: Icon(Icons.arrow_downward), selectedIcon: Icon(Icons.arrow_downward), label: 'Mapato'),
          NavigationDestination(icon: Icon(Icons.arrow_upward), selectedIcon: Icon(Icons.arrow_upward), label: 'Matumizi'),
          NavigationDestination(icon: Icon(Icons.pie_chart_outline), selectedIcon: Icon(Icons.pie_chart), label: 'Bajeti'),
        ],
      ),
    );
  }
}
