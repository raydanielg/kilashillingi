import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../auth/presentation/auth_state.dart';
import '../../budgets/presentation/budgets_screen.dart';
import '../../profile/presentation/profile_screen.dart';
import '../../transactions/presentation/transactions_screen.dart';
import '../../transactions/presentation/transactions_controller.dart';
import '../../../app/app.dart';
import 'dashboard_screen.dart';
import 'header.dart';

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
                const SizedBox(height: 12),
                FilledButton.icon(
                  onPressed: () async {
                    Navigator.of(context).pop();
                    _goTo(1);
                    await Future<void>.delayed(const Duration(milliseconds: 250));
                    if (!mounted) return;
                    await _openQuickTransactionDialog(type: 'income');
                  },
                  icon: const Icon(Icons.arrow_downward),
                  label: const Text('Mapato'),
                ),
                const SizedBox(height: 10),
                FilledButton.tonalIcon(
                  onPressed: () async {
                    Navigator.of(context).pop();
                    _goTo(2);
                    await Future<void>.delayed(const Duration(milliseconds: 250));
                    if (!mounted) return;
                    await _openQuickTransactionDialog(type: 'expense');
                  },
                  icon: const Icon(Icons.arrow_upward),
                  label: const Text('Matumizi'),
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
                  InkWell(
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(builder: (_) => const ProfileScreen(embedded: false)),
                    ),
                    borderRadius: BorderRadius.circular(999),
                    child: CircleAvatar(
                      radius: 18,
                      backgroundColor: Theme.of(context).colorScheme.primary.withValues(alpha: 0.12),
                      backgroundImage: avatarUrl.isNotEmpty ? NetworkImage(avatarUrl) : null,
                      child: avatarUrl.isNotEmpty
                          ? null
                          : Text(
                              initials,
                              style: TextStyle(
                                fontWeight: FontWeight.w900,
                                color: Theme.of(context).colorScheme.primary,
                              ),
                            ),
                    ),
                  ),
                ],
              ),
            ),
            const Divider(height: 1),
            Expanded(
              child: IndexedStack(
                index: _index,
                children: pages,
              ),
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
