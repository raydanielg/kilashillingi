import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../auth/presentation/auth_state.dart';
import '../../budgets/presentation/budgets_screen.dart';
import '../../profile/presentation/profile_screen.dart';
import '../../transactions/presentation/transactions_screen.dart';
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

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authStateProvider);

    if (auth.status != AuthStatus.authenticated) {
      return const DashboardScreen(embedded: false);
    }

    final name = (auth.user?['name'] ?? '').toString();
    final currency = (auth.user?['currency'] ?? 'KSh').toString();

    final titles = <String>[
      'Dashboard',
      'Transactions',
      'Budgets',
      'Profile',
    ];

    final subtitles = <String>[
      name.isEmpty ? 'Karibu' : 'Karibu, $name',
      'Income & Expense',
      'Mipango ya matumizi',
      'Settings & $currency',
    ];

    final pages = <Widget>[
      DashboardScreen(embedded: true, onShortcut: _goTo),
      const TransactionsScreen(embedded: true),
      const BudgetsScreen(embedded: true),
      const ProfileScreen(embedded: true),
    ];

    return Scaffold(
      body: SafeArea(
        child: Column(
          children: [
            Header(
              title: titles[_index],
              subtitle: subtitles[_index],
              trailing: IconButton(
                tooltip: 'Profile',
                onPressed: () => _goTo(3),
                icon: const Icon(Icons.person_outline),
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
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: _goTo,
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home_outlined), selectedIcon: Icon(Icons.home), label: 'Home'),
          NavigationDestination(icon: Icon(Icons.swap_horiz), selectedIcon: Icon(Icons.swap_horiz), label: 'Tx'),
          NavigationDestination(icon: Icon(Icons.pie_chart_outline), selectedIcon: Icon(Icons.pie_chart), label: 'Budgets'),
          NavigationDestination(icon: Icon(Icons.person_outline), selectedIcon: Icon(Icons.person), label: 'Me'),
        ],
      ),
    );
  }
}
