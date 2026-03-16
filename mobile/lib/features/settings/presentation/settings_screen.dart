import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../auth/presentation/auth_controller.dart';

class SettingsScreen extends ConsumerWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

    final auth = ref.watch(authStateProvider);
    final user = auth.user ?? <String, dynamic>{};

    final name = (user['name'] ?? '').toString();
    final email = (user['email'] ?? '').toString();
    final currency = (user['currency'] ?? '').toString();

    return Scaffold(
      appBar: AppBar(
        title: const Text('Settings'),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: surface,
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: outline),
              gradient: LinearGradient(
                colors: [
                  primary.withValues(alpha: 0.10),
                  Colors.purple.withValues(alpha: 0.06),
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            child: Row(
              children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    color: primary.withValues(alpha: 0.14),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Icon(Icons.settings, color: primary),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        name.isEmpty ? 'Account' : name,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: theme.textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        email.isEmpty ? (currency.isEmpty ? '' : currency) : '$email${currency.isEmpty ? '' : ' • $currency'}',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 14),
          _Section(
            title: 'Account',
            children: [
              _Tile(
                icon: Icons.person_outline,
                title: 'Profile',
                subtitle: 'Badili taarifa zako binafsi',
                onTap: () => context.push('/profile'),
              ),
            ],
          ),
          const SizedBox(height: 12),
          _Section(
            title: 'Legal',
            children: [
              _Tile(
                icon: Icons.privacy_tip_outlined,
                title: 'Privacy Policy',
                subtitle: 'Sera ya faragha',
                onTap: () => _openSimpleDialog(
                  context,
                  title: 'Privacy Policy',
                  message: 'Sera ya faragha inapatikana kwenye website (Privacy page).',
                ),
              ),
              _Tile(
                icon: Icons.article_outlined,
                title: 'Terms & Conditions',
                subtitle: 'Masharti na vigezo',
                onTap: () => _openSimpleDialog(
                  context,
                  title: 'Terms & Conditions',
                  message: 'Masharti na vigezo vinapatikana kwenye website.',
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          _Section(
            title: 'About',
            children: [
              _Tile(
                icon: Icons.info_outline,
                title: 'About KilaShillingi',
                subtitle: 'Maelezo mafupi ya app',
                onTap: () => _openSimpleDialog(
                  context,
                  title: 'About',
                  message: 'KilaShillingi ni app ya kusaidia usimamizi wa fedha: mapato, matumizi, bajeti na malengo.',
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          SizedBox(
            height: 52,
            child: FilledButton.tonalIcon(
              onPressed: () async {
                final ok = await showDialog<bool>(
                      context: context,
                      builder: (_) => AlertDialog(
                        title: const Text('Logout?'),
                        content: const Text('Unataka kutoka kwenye akaunti?'),
                        actions: [
                          TextButton(onPressed: () => Navigator.of(context).pop(false), child: const Text('Ghairi')),
                          FilledButton(onPressed: () => Navigator.of(context).pop(true), child: const Text('Logout')),
                        ],
                      ),
                    ) ??
                    false;
                if (!ok) return;
                await ref.read(authStateProvider.notifier).logout();
                if (context.mounted) context.go('/login');
              },
              icon: const Icon(Icons.logout),
              label: const Text('Logout'),
              style: FilledButton.styleFrom(
                backgroundColor: Colors.red.withValues(alpha: 0.12),
                foregroundColor: Colors.red.shade700,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _Section extends StatelessWidget {
  const _Section({required this.title, required this.children});

  final String title;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final surface = theme.colorScheme.surfaceContainerHighest;
    final outline = theme.colorScheme.outlineVariant.withValues(alpha: 0.35);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(title, style: theme.textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w900)),
        const SizedBox(height: 8),
        Container(
          decoration: BoxDecoration(
            color: surface,
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: outline),
          ),
          child: Column(
            children: [
              for (int i = 0; i < children.length; i++) ...[
                if (i > 0)
                  Divider(height: 1, thickness: 1, color: outline.withValues(alpha: 0.5)),
                children[i],
              ],
            ],
          ),
        ),
      ],
    );
  }
}

class _Tile extends StatelessWidget {
  const _Tile({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final primary = theme.colorScheme.primary;

    return ListTile(
      onTap: onTap,
      leading: Container(
        width: 42,
        height: 42,
        decoration: BoxDecoration(
          color: primary.withValues(alpha: 0.12),
          borderRadius: BorderRadius.circular(16),
        ),
        child: Icon(icon, color: primary),
      ),
      title: Text(title, style: theme.textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w900)),
      subtitle: Text(subtitle, style: theme.textTheme.labelSmall?.copyWith(color: theme.hintColor)),
      trailing: Icon(Icons.chevron_right, color: theme.hintColor),
    );
  }
}

Future<void> _openSimpleDialog(BuildContext context, {required String title, required String message}) async {
  await showDialog<void>(
    context: context,
    builder: (_) => AlertDialog(
      title: Text(title),
      content: Text(message),
      actions: [
        FilledButton(onPressed: () => Navigator.of(context).pop(), child: const Text('Sawa')),
      ],
    ),
  );
}
