import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../auth/presentation/auth_state.dart';
import 'dashboard_controller.dart';

class DashboardScreen extends ConsumerWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final auth = ref.watch(authStateProvider);
    final summaryAsync = ref.watch(dashboardSummaryProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            tooltip: 'Profile',
            onPressed: () => context.go('/profile'),
            icon: const Icon(Icons.person),
          ),
          IconButton(
            tooltip: 'Logout',
            onPressed: () async {
              await ref.read(authStateProvider.notifier).logout();
              if (context.mounted) context.go('/login');
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      )
      ,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (auth.status != AuthStatus.authenticated) ...[
                const Text('Not authenticated.'),
                const SizedBox(height: 10),
                FilledButton(
                  onPressed: () => context.go('/login'),
                  child: const Text('Go to Login'),
                ),
              ] else ...[
                Text(
                  'Karibu, ${(auth.user?['name'] ?? '').toString()}',
                  style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 14),
                Row(
                  children: [
                    Expanded(
                      child: _NavCard(
                        title: 'Transactions',
                        subtitle: 'Income & Expense',
                        icon: Icons.swap_horiz,
                        onTap: () => context.go('/transactions'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _NavCard(
                        title: 'Budgets',
                        subtitle: 'This month',
                        icon: Icons.pie_chart_outline,
                        onTap: () => context.go('/budgets'),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Expanded(
                  child: summaryAsync.when(
                    data: (data) {
                      final currency = (data['currency'] ?? (auth.user?['currency'] ?? 'KSh')).toString();
                      final totals = Map<String, dynamic>.from(data['totals'] as Map);
                      final income = (totals['income'] as num?)?.toDouble() ?? 0;
                      final expense = (totals['expense'] as num?)?.toDouble() ?? 0;
                      final balance = (totals['balance'] as num?)?.toDouble() ?? 0;
                      final monthIncome = (totals['month_income'] as num?)?.toDouble() ?? 0;
                      final monthExpense = (totals['month_expense'] as num?)?.toDouble() ?? 0;

                      final recent = (data['recent_transactions'] as List)
                          .map((e) => Map<String, dynamic>.from(e as Map))
                          .toList();

                      return ListView(
                        children: [
                          _TotalsGrid(
                            currency: currency,
                            income: income,
                            expense: expense,
                            balance: balance,
                            monthIncome: monthIncome,
                            monthExpense: monthExpense,
                          ),
                          const SizedBox(height: 16),
                          Text(
                            'Recent',
                            style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w800),
                          ),
                          const SizedBox(height: 8),
                          if (recent.isEmpty)
                            const Text('No transactions yet.')
                          else
                            ...recent.map((tx) {
                              final type = (tx['type'] ?? '').toString();
                              final amount = (tx['amount'] ?? '').toString();
                              final desc = (tx['description'] ?? '').toString();
                              final date = (tx['date'] ?? '').toString();
                              final color = type == 'income' ? Colors.green : Colors.red;

                              return Card(
                                child: ListTile(
                                  leading: CircleAvatar(
                                    backgroundColor: color.withValues(alpha: 0.12),
                                    child: Icon(
                                      type == 'income' ? Icons.arrow_downward : Icons.arrow_upward,
                                      color: color,
                                    ),
                                  ),
                                  title: Text('$currency $amount'),
                                  subtitle: Text(desc.isEmpty ? date : '$desc\n$date'),
                                  isThreeLine: desc.isNotEmpty,
                                ),
                              );
                            }),
                        ],
                      );
                    },
                    loading: () => const Center(child: CircularProgressIndicator()),
                    error: (e, _) => Center(
                      child: Text('Failed to load dashboard: $e'),
                    ),
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

class _NavCard extends StatelessWidget {
  const _NavCard({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.onTap,
  });

  final String title;
  final String subtitle;
  final IconData icon;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Ink(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          color: Theme.of(context).colorScheme.surfaceContainerHighest,
        ),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Row(
            children: [
              CircleAvatar(
                backgroundColor: Theme.of(context).colorScheme.primary.withValues(alpha: 0.12),
                child: Icon(icon, color: Theme.of(context).colorScheme.primary),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title, style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w800)),
                    const SizedBox(height: 2),
                    Text(subtitle, style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.black54)),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _TotalsGrid extends StatelessWidget {
  const _TotalsGrid({
    required this.currency,
    required this.income,
    required this.expense,
    required this.balance,
    required this.monthIncome,
    required this.monthExpense,
  });

  final String currency;
  final double income;
  final double expense;
  final double balance;
  final double monthIncome;
  final double monthExpense;

  @override
  Widget build(BuildContext context) {
    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: 2,
      childAspectRatio: 1.55,
      crossAxisSpacing: 12,
      mainAxisSpacing: 12,
      children: [
        _TotalTile(label: 'Balance', value: '$currency ${balance.toStringAsFixed(0)}'),
        _TotalTile(label: 'Income', value: '$currency ${income.toStringAsFixed(0)}'),
        _TotalTile(label: 'Expense', value: '$currency ${expense.toStringAsFixed(0)}'),
        _TotalTile(label: 'This month', value: '$currency ${(monthIncome - monthExpense).toStringAsFixed(0)}'),
      ],
    );
  }
}

class _TotalTile extends StatelessWidget {
  const _TotalTile({required this.label, required this.value});

  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.black12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(label, style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.black54)),
          const SizedBox(height: 8),
          Text(value, style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900)),
        ],
      ),
    );
  }
}
