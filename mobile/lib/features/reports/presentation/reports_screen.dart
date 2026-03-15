import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

class ReportsScreen extends ConsumerWidget {
  const ReportsScreen({super.key, this.embedded = false});

  final bool embedded;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;

    final reportTypes = [
      {'title': 'Ripoti ya Siku', 'subtitle': 'Muhtasari wa leo', 'icon': Icons.today, 'color': Colors.blue},
      {'title': 'Ripoti ya Wiki', 'subtitle': 'Mwenendo wa wiki hii', 'icon': Icons.date_range, 'color': Colors.green},
      {'title': 'Ripoti ya Mwezi', 'subtitle': 'Mchanganuo wa mwezi huu', 'icon': Icons.calendar_month, 'color': Colors.orange},
      {'title': 'Ripoti ya Mwaka', 'subtitle': 'Mapitio ya mwaka mzima', 'icon': Icons.analytics, 'color': Colors.purple},
    ];

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Aina za Ripoti',
              style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            Expanded(
              child: GridView.builder(
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                  childAspectRatio: 1.1,
                ),
                itemCount: reportTypes.length,
                itemBuilder: (context, index) {
                  final type = reportTypes[index];
                  final color = type['color'] as Color;
                  return InkWell(
                    onTap: () {
                      // TODO: Implement navigation to specific report
                    },
                    borderRadius: BorderRadius.circular(20),
                    child: Container(
                      decoration: BoxDecoration(
                        color: color.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: color.withValues(alpha: 0.2), width: 1.5),
                      ),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: color.withValues(alpha: 0.2),
                              shape: BoxShape.circle,
                            ),
                            child: Icon(type['icon'] as IconData, color: color, size: 28),
                          ),
                          const SizedBox(height: 12),
                          Text(
                            type['title'] as String,
                            style: theme.textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            type['subtitle'] as String,
                            style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}
