import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../auth/presentation/auth_state.dart';
import 'dashboard_controller.dart';

class DashboardScreen extends ConsumerWidget {
  const DashboardScreen({super.key, this.embedded = false, this.onShortcut});

  final bool embedded;
  final void Function(int index)? onShortcut;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final auth = ref.watch(authStateProvider);
    final summaryAsync = ref.watch(dashboardSummaryProvider);
    final theme = Theme.of(context);

    final body = Padding(
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (auth.status != AuthStatus.authenticated) ...[
            const Text('Hujaingia kwenye akaunti.'),
            const SizedBox(height: 10),
            FilledButton(
              onPressed: () => context.go('/login'),
              child: const Text('Ingia'),
            ),
          ] else ...[
            _HeroWelcomeCard(
              name: (auth.user?['name'] ?? '').toString(),
              currency: (auth.user?['currency'] ?? 'KSh').toString(),
              summaryAsync: summaryAsync,
            ),
            const SizedBox(height: 14),
            Expanded(
              child: summaryAsync.when(
                data: (data) {
                  final currency = (data['currency'] ?? (auth.user?['currency'] ?? 'KSh')).toString();
                  final totalsRaw = data['totals'];
                  final totals = totalsRaw is Map ? Map<String, dynamic>.from(totalsRaw) : <String, dynamic>{};
                  final income = (totals['income'] as num?)?.toDouble() ?? 0;
                  final expense = (totals['expense'] as num?)?.toDouble() ?? 0;
                  final balance = (totals['balance'] as num?)?.toDouble() ?? 0;
                  final monthIncome = (totals['month_income'] as num?)?.toDouble() ?? 0;
                  final monthExpense = (totals['month_expense'] as num?)?.toDouble() ?? 0;

                  final isCached = data['is_cached'] == true;
                  final cachedAt = (data['cached_at'] ?? '').toString();

                  final recent = (data['recent_transactions'] as List)
                      .map((e) => Map<String, dynamic>.from(e as Map))
                      .toList();

                  return ListView(
                    children: [
                      if (isCached)
                        Card(
                          child: Padding(
                            padding: const EdgeInsets.all(12),
                            child: Row(
                              children: [
                                const Icon(Icons.wifi_off, size: 18),
                                const SizedBox(width: 8),
                                Expanded(
                                  child: Text(
                                    cachedAt.isEmpty
                                        ? 'Unaona data ya mwisho iliyohifadhiwa (offline).'
                                        : 'Unaona data ya mwisho iliyohifadhiwa (offline)\nMuda: $cachedAt',
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      if (isCached) const SizedBox(height: 12),
                      _TodayStatsScroll(
                        currency: currency,
                        todayIncome: (totals['today_income'] as num?)?.toDouble() ?? 0,
                        todayExpense: (totals['today_expense'] as num?)?.toDouble() ?? 0,
                        todayTx: (totals['today_transactions_count'] as num?)?.toInt() ?? 0,
                        monthIncome: monthIncome,
                        monthExpense: monthExpense,
                      ),
                      const SizedBox(height: 16),
                      _TrendsCard(
                        currency: currency,
                        trends: data['trends'],
                      ),
                      const SizedBox(height: 16),
                      Text(
                        'Miamala ya hivi karibuni',
                        style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900),
                      ),
                      const SizedBox(height: 8),
                      if (recent.isEmpty)
                        const Text('Bado hakuna miamala.')
                      else
                        ...recent.map((tx) {
                          final type = (tx['type'] ?? '').toString();
                          final amount = (tx['amount'] ?? '').toString();
                          final desc = (tx['description'] ?? '').toString();
                          final date = (tx['date'] ?? '').toString();
                          final color = type == 'income' ? Colors.green : Colors.red;

                          return Card(
                            elevation: 0,
                            margin: const EdgeInsets.symmetric(vertical: 4),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                              side: BorderSide(color: theme.dividerColor.withValues(alpha: 0.05)),
                            ),
                            child: ListTile(
                              visualDensity: VisualDensity.compact,
                              contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 0),
                              leading: CircleAvatar(
                                radius: 16,
                                backgroundColor: color.withValues(alpha: 0.1),
                                child: Icon(
                                  type == 'income' ? Icons.arrow_downward : Icons.arrow_upward,
                                  color: color,
                                  size: 16,
                                ),
                              ),
                              title: Text(
                                '$currency ${amount.toString()}',
                                style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w800),
                              ),
                              subtitle: Text(
                                desc.isEmpty ? date : desc,
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                style: theme.textTheme.labelSmall,
                              ),
                              trailing: Text(
                                date,
                                style: theme.textTheme.labelSmall?.copyWith(color: theme.disabledColor),
                              ),
                            ),
                          );
                        }),
                    ],
                  );
                },
                loading: () => const Center(child: CircularProgressIndicator()),
                error: (e, _) => _DashboardError(
                  message: e.toString().replaceFirst('Exception: ', ''),
                  onRetry: () => ref.invalidate(dashboardSummaryProvider),
                  onLogin: () => context.go('/login'),
                  onTransactions: () {
                    if (onShortcut != null) {
                      onShortcut!(1);
                    } else {
                      context.go('/transactions');
                    }
                  },
                ),
              ),
            ),
          ],
        ],
      ),
    );

    if (embedded) {
      return body;
    }

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
      ),
      body: SafeArea(child: body),
    );
  }
}

class _DashboardError extends StatelessWidget {
  const _DashboardError({
    required this.message,
    required this.onRetry,
    required this.onLogin,
    required this.onTransactions,
  });

  final String message;
  final VoidCallback onRetry;
  final VoidCallback onLogin;
  final VoidCallback onTransactions;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    final needsLogin = message.toLowerCase().contains('login') || message.toLowerCase().contains('unauthorized');

    return Center(
      child: ConstrainedBox(
        constraints: const BoxConstraints(maxWidth: 520),
        child: Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Imeshindikana kupakia dashboard',
                  style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900),
                ),
                const SizedBox(height: 8),
                Text(message, style: theme.textTheme.bodyMedium?.copyWith(color: Colors.black87)),
                const SizedBox(height: 14),
                Wrap(
                  spacing: 10,
                  runSpacing: 10,
                  children: [
                    FilledButton(
                      style: FilledButton.styleFrom(minimumSize: const Size(0, 52)),
                      onPressed: onRetry,
                      child: const Text('Jaribu tena'),
                    ),
                    TextButton(
                      onPressed: onTransactions,
                      child: const Text('Nenda Miamala'),
                    ),
                    if (needsLogin)
                      TextButton(
                        onPressed: onLogin,
                        child: const Text('Ingia'),
                      ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _HeroWelcomeCard extends StatefulWidget {
  const _HeroWelcomeCard({
    required this.name,
    required this.currency,
    required this.summaryAsync,
  });

  final String name;
  final String currency;
  final AsyncValue<Map<String, dynamic>> summaryAsync;

  @override
  State<_HeroWelcomeCard> createState() => _HeroWelcomeCardState();
}

class _HeroWelcomeCardState extends State<_HeroWelcomeCard> {
  bool _showBalance = false;
  bool _revealLoading = false;

  Future<void> _toggleBalance() async {
    if (_revealLoading) return;

    if (_showBalance) {
      setState(() {
        _showBalance = false;
        _revealLoading = false;
      });
      return;
    }

    setState(() {
      _revealLoading = true;
    });

    await Future.delayed(const Duration(milliseconds: 800));
    if (!mounted) return;

    setState(() {
      _showBalance = true;
      _revealLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;
    final onPrimary = theme.colorScheme.onPrimary;

    final displayName = widget.name.trim().isEmpty ? 'Mtumiaji' : widget.name.trim();

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        gradient: LinearGradient(
          colors: [
            primary,
            Color.lerp(primary, Colors.red.shade700, 0.35)!,
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: primary.withValues(alpha: 0.25),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Hello 👋, $displayName',
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: theme.textTheme.titleLarge?.copyWith(
              color: onPrimary,
              fontWeight: FontWeight.w900,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            'Karibu kwenye KilaShillingi. Rekodi mapato na matumizi yako kila siku.',
            style: theme.textTheme.bodyMedium?.copyWith(
              color: onPrimary.withValues(alpha: 0.9),
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 14),
          widget.summaryAsync.when(
            data: (data) {
              final totalsRaw = data['totals'];
              final totals = totalsRaw is Map ? Map<String, dynamic>.from(totalsRaw) : <String, dynamic>{};
              final balance = (totals['balance'] as num?)?.toDouble() ?? 0;

              return Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          'Salio la sasa',
                          style: theme.textTheme.bodySmall?.copyWith(
                            color: onPrimary.withValues(alpha: 0.85),
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                      InkWell(
                        onTap: _toggleBalance,
                        borderRadius: BorderRadius.circular(999),
                        child: Padding(
                          padding: const EdgeInsets.all(8),
                          child: Icon(
                            _showBalance ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                            size: 20,
                            color: onPrimary.withValues(alpha: 0.95),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  if (_revealLoading)
                    const _BalanceSkeleton()
                  else
                    Text(
                      _showBalance
                          ? '${widget.currency} ${balance.toStringAsFixed(0)}'
                          : '${widget.currency} ••••••',
                      style: theme.textTheme.headlineSmall?.copyWith(
                        color: onPrimary,
                        fontWeight: FontWeight.w900,
                        letterSpacing: _showBalance ? 0 : 1.5,
                      ),
                    ),
                ],
              );
            },
            loading: () => Padding(
              padding: const EdgeInsets.symmetric(vertical: 6),
              child: Row(
                children: [
                  SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                      strokeWidth: 2.2,
                      valueColor: AlwaysStoppedAnimation<Color>(onPrimary.withValues(alpha: 0.9)),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Text(
                    'Inapakia muhtasari...',
                    style: theme.textTheme.bodyMedium?.copyWith(color: onPrimary.withValues(alpha: 0.9)),
                  ),
                ],
              ),
            ),
            error: (_, __) => Text(
              'Imeshindikana kupata muhtasari. Jaribu tena.',
              style: theme.textTheme.bodyMedium?.copyWith(color: onPrimary.withValues(alpha: 0.9)),
            ),
          ),
        ],
      ),
    );
  }
}

class _BalanceSkeleton extends StatefulWidget {
  const _BalanceSkeleton();

  @override
  State<_BalanceSkeleton> createState() => _BalanceSkeletonState();
}

class _BalanceSkeletonState extends State<_BalanceSkeleton> with SingleTickerProviderStateMixin {
  late final AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(vsync: this, duration: const Duration(milliseconds: 900))..repeat(reverse: true);
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final onPrimary = Theme.of(context).colorScheme.onPrimary;

    return AnimatedBuilder(
      animation: _controller,
      builder: (context, _) {
        final a = 0.18 + (_controller.value * 0.18);
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              height: 26,
              width: 180,
              decoration: BoxDecoration(
                color: onPrimary.withValues(alpha: a),
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            const SizedBox(height: 8),
            Container(
              height: 14,
              width: 120,
              decoration: BoxDecoration(
                color: onPrimary.withValues(alpha: a * 0.85),
                borderRadius: BorderRadius.circular(10),
              ),
            ),
          ],
        );
      },
    );
  }
}

class _HeroPill extends StatelessWidget {
  const _HeroPill({required this.label, required this.value});

  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.white.withValues(alpha: 0.18)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            label,
            style: theme.textTheme.bodySmall?.copyWith(
              color: theme.colorScheme.onPrimary.withValues(alpha: 0.9),
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            value,
            style: theme.textTheme.bodyMedium?.copyWith(
              color: theme.colorScheme.onPrimary,
              fontWeight: FontWeight.w900,
            ),
          ),
        ],
      ),
    );
  }
}

class _TrendsCard extends StatelessWidget {
  const _TrendsCard({
    required this.currency,
    required this.trends,
  });

  final String currency;
  final Object? trends;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    if (trends is! Map) {
      return const SizedBox.shrink();
    }

    final m = Map<String, dynamic>.from(trends as Map);
    final labelsRaw = m['labels'];
    final incomeRaw = m['income'];
    final expenseRaw = m['expense'];

    if (labelsRaw is! List || incomeRaw is! List || expenseRaw is! List) {
      return const SizedBox.shrink();
    }

    final labels = labelsRaw.map((e) => e.toString()).toList();
    final income = incomeRaw.map((e) => (e is num) ? e.toDouble() : double.tryParse(e.toString()) ?? 0).toList();
    final expense = expenseRaw.map((e) => (e is num) ? e.toDouble() : double.tryParse(e.toString()) ?? 0).toList();

    final len = [labels.length, income.length, expense.length].reduce((a, b) => a < b ? a : b);
    if (len == 0) return const SizedBox.shrink();

    final maxV = <double>[...income.take(len), ...expense.take(len)].fold<double>(0, (p, v) => v > p ? v : p);
    final safeMax = maxV <= 0 ? 1.0 : maxV;

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Text(
                  'Mwenendo (Siku 7)',
                  style: theme.textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w900),
                ),
                const Spacer(),
                _LegendDot(color: Colors.green, label: 'Mapato'),
                const SizedBox(width: 10),
                _LegendDot(color: Colors.red, label: 'Matumizi'),
              ],
            ),
            const SizedBox(height: 12),
            SizedBox(
              height: 120,
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: List.generate(len, (i) {
                  final inV = income[i];
                  final exV = expense[i];
                  final inH = (inV / safeMax).clamp(0, 1);
                  final exH = (exV / safeMax).clamp(0, 1);

                  return Expanded(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 4),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.end,
                        children: [
                          Expanded(
                            child: Row(
                              crossAxisAlignment: CrossAxisAlignment.end,
                              children: [
                                Expanded(
                                  child: Container(
                                    height: double.infinity,
                                    alignment: Alignment.bottomCenter,
                                    child: FractionallySizedBox(
                                      heightFactor: inH.toDouble(),
                                      child: Container(
                                        decoration: BoxDecoration(
                                          color: Colors.green.withValues(alpha: 0.75),
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 4),
                                Expanded(
                                  child: Container(
                                    height: double.infinity,
                                    alignment: Alignment.bottomCenter,
                                    child: FractionallySizedBox(
                                      heightFactor: exH.toDouble(),
                                      child: Container(
                                        decoration: BoxDecoration(
                                          color: Colors.red.withValues(alpha: 0.75),
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            labels[i],
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: theme.textTheme.labelSmall?.copyWith(color: theme.colorScheme.onSurfaceVariant),
                          ),
                        ],
                      ),
                    ),
                  );
                }),
              ),
            ),
            const SizedBox(height: 10),
            Text(
              'Max: $currency ${safeMax.toStringAsFixed(0)}',
              style: theme.textTheme.bodySmall?.copyWith(color: theme.colorScheme.onSurfaceVariant),
            ),
          ],
        ),
      ),
    );
  }
}

class _LegendDot extends StatelessWidget {
  const _LegendDot({required this.color, required this.label});

  final Color color;
  final String label;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 10,
          height: 10,
          decoration: BoxDecoration(
            color: color,
            borderRadius: BorderRadius.circular(99),
          ),
        ),
        const SizedBox(width: 6),
        Text(
          label,
          style: theme.textTheme.labelSmall?.copyWith(fontWeight: FontWeight.w800),
        ),
      ],
    );
  }
}

class _Shortcuts extends StatelessWidget {
  const _Shortcuts({
    required this.onTransactions,
    required this.onBudgets,
    required this.onProfile,
  });

  final VoidCallback onTransactions;
  final VoidCallback onBudgets;
  final VoidCallback onProfile;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: 3,
      childAspectRatio: 1.05,
      crossAxisSpacing: 10,
      mainAxisSpacing: 10,
      children: [
        _ShortcutTile(
          title: 'Transactions',
          icon: Icons.swap_horiz,
          color: theme.colorScheme.primary,
          onTap: onTransactions,
        ),
        _ShortcutTile(
          title: 'Budgets',
          icon: Icons.pie_chart_outline,
          color: theme.colorScheme.primary,
          onTap: onBudgets,
        ),
        _ShortcutTile(
          title: 'Profile',
          icon: Icons.person_outline,
          color: theme.colorScheme.primary,
          onTap: onProfile,
        ),
      ],
    );
  }
}

class _ShortcutTile extends StatelessWidget {
  const _ShortcutTile({
    required this.title,
    required this.icon,
    required this.color,
    required this.onTap,
  });

  final String title;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircleAvatar(
                radius: 20,
                backgroundColor: color.withValues(alpha: 0.12),
                child: Icon(icon, color: color),
              ),
              const SizedBox(height: 10),
              Text(
                title,
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: Theme.of(context).textTheme.bodySmall?.copyWith(fontWeight: FontWeight.w800),
              ),
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

class _TodayStatsScroll extends StatelessWidget {
  const _TodayStatsScroll({
    required this.currency,
    required this.todayIncome,
    required this.todayExpense,
    required this.todayTx,
    required this.monthIncome,
    required this.monthExpense,
  });

  final String currency;
  final double todayIncome;
  final double todayExpense;
  final int todayTx;
  final double monthIncome;
  final double monthExpense;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          height: 110,
          child: ListView(
            scrollDirection: Axis.horizontal,
            padding: EdgeInsets.zero,
            children: [
              _StatCard(
                label: 'Mapato (Leo)',
                value: '$currency ${todayIncome.toStringAsFixed(0)}',
                color: Colors.green,
                icon: Icons.arrow_downward,
              ),
              const SizedBox(width: 12),
              _StatCard(
                label: 'Matumizi (Leo)',
                value: '$currency ${todayExpense.toStringAsFixed(0)}',
                color: Colors.red,
                icon: Icons.arrow_upward,
              ),
              const SizedBox(width: 12),
              _StatCard(
                label: 'Miamala (Leo)',
                value: todayTx.toString(),
                color: Colors.blue,
                icon: Icons.receipt_long,
              ),
              const SizedBox(width: 12),
              _StatCard(
                label: 'Mapato (Mwezi)',
                value: '$currency ${monthIncome.toStringAsFixed(0)}',
                color: Colors.teal,
                icon: Icons.account_balance_wallet,
              ),
              const SizedBox(width: 12),
              _StatCard(
                label: 'Matumizi (Mwezi)',
                value: '$currency ${monthExpense.toStringAsFixed(0)}',
                color: Colors.orange,
                icon: Icons.payments,
              ),
            ],
          ),
        ),
      ],
    );
  }
}

class _StatCard extends StatelessWidget {
  const _StatCard({
    required this.label,
    required this.value,
    required this.color,
    required this.icon,
  });

  final String label;
  final String value;
  final Color color;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Container(
      width: 150,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withValues(alpha: 0.2), width: 1.5),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(4),
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.2),
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, size: 16, color: color),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: theme.textTheme.labelSmall?.copyWith(
                    color: color.withValues(alpha: 0.8),
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            value,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: theme.textTheme.titleMedium?.copyWith(
              color: color,
              fontWeight: FontWeight.w900,
              letterSpacing: -0.5,
            ),
          ),
        ],
      ),
    );
  }
}
