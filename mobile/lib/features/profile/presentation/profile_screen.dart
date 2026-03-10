import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../auth/presentation/currency_provider.dart';
import 'profile_controller.dart';

class ProfileScreen extends ConsumerStatefulWidget {
  const ProfileScreen({super.key, this.embedded = false});

  final bool embedded;

  @override
  ConsumerState<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends ConsumerState<ProfileScreen> {
  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();

  String? _currency;
  bool _saving = false;

  @override
  void dispose() {
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _phoneCtrl.dispose();
    super.dispose();
  }

  String _initials(String nameOrEmail) {
    final s = nameOrEmail.trim();
    if (s.isEmpty) return 'U';

    final parts = s.split(RegExp(r'\s+')).where((p) => p.isNotEmpty).toList();
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    }

    return s.substring(0, s.length >= 2 ? 2 : 1).toUpperCase();
  }

  String? _avatarUrl(Map<String, dynamic> user) {
    final keys = ['avatar_url', 'avatar', 'photo_url', 'profile_photo_url', 'image_url'];
    for (final k in keys) {
      final v = user[k];
      if (v == null) continue;
      final s = v.toString().trim();
      if (s.isEmpty) continue;
      return s;
    }
    return null;
  }

  Future<bool> _confirmCurrencyChange(BuildContext context) async {
    return (await showDialog<bool>(
          context: context,
          builder: (context) {
            return AlertDialog(
              title: const Text('Badili currency?'),
              content: const Text(
                'Ukibadilisha currency, data zako zote za zamani (transactions, budgets, debts, reminders) zitafutwa ili kuepuka kuchanganya currency. Unaendelea?',
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.of(context).pop(false),
                  child: const Text('Hapana'),
                ),
                FilledButton(
                  onPressed: () => Navigator.of(context).pop(true),
                  child: const Text('Ndio, endelea'),
                ),
              ],
            );
          },
        )) ??
        false;
  }

  @override
  Widget build(BuildContext context) {
    final profileAsync = ref.watch(profileProvider);
    final currenciesAsync = ref.watch(currenciesProvider);

    final body = profileAsync.when(
          data: (user) {
            _nameCtrl.text = _nameCtrl.text.isEmpty ? (user['name'] ?? '').toString() : _nameCtrl.text;
            _emailCtrl.text = _emailCtrl.text.isEmpty ? (user['email'] ?? '').toString() : _emailCtrl.text;
            _phoneCtrl.text = _phoneCtrl.text.isEmpty ? (user['phone'] ?? '').toString() : _phoneCtrl.text;
            _currency ??= (user['currency'] ?? 'KSh').toString();

            final oldCurrency = (user['currency'] ?? 'KSh').toString();

            final avatar = _avatarUrl(user);
            final displayName = _nameCtrl.text.trim().isNotEmpty ? _nameCtrl.text.trim() : (user['name'] ?? '').toString();
            final displayEmail = _emailCtrl.text.trim().isNotEmpty ? _emailCtrl.text.trim() : (user['email'] ?? '').toString();
            final displayPhone = _phoneCtrl.text.trim().isNotEmpty ? _phoneCtrl.text.trim() : (user['phone'] ?? '').toString();

            return ListView(
              padding: const EdgeInsets.all(20),
              children: [
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        CircleAvatar(
                          radius: 34,
                          backgroundColor: Theme.of(context).colorScheme.primary.withValues(alpha: 0.12),
                          backgroundImage: (avatar != null && avatar.startsWith('http')) ? NetworkImage(avatar) : null,
                          child: (avatar == null || !avatar.startsWith('http'))
                              ? Text(
                                  _initials(displayName.isNotEmpty ? displayName : displayEmail),
                                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                        fontWeight: FontWeight.w900,
                                        color: Theme.of(context).colorScheme.primary,
                                      ),
                                )
                              : null,
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                displayName.isEmpty ? 'User' : displayName,
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900),
                              ),
                              const SizedBox(height: 2),
                              if (displayPhone.isNotEmpty)
                                Text(
                                  displayPhone,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Colors.black54),
                                ),
                              if (displayEmail.isNotEmpty)
                                Text(
                                  displayEmail,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.black54),
                                ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 14),
                Text(
                  'Taarifa binafsi',
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w900),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: _nameCtrl,
                  decoration: const InputDecoration(labelText: 'Jina', prefixIcon: Icon(Icons.person_outline)),
                ),
                const SizedBox(height: 14),
                TextField(
                  controller: _emailCtrl,
                  keyboardType: TextInputType.emailAddress,
                  decoration: const InputDecoration(labelText: 'Email', prefixIcon: Icon(Icons.email_outlined)),
                ),
                const SizedBox(height: 14),
                TextField(
                  controller: _phoneCtrl,
                  keyboardType: TextInputType.phone,
                  decoration: const InputDecoration(labelText: 'Simu (mf 07XXXXXXXX)', prefixIcon: Icon(Icons.phone_iphone)),
                ),
                const SizedBox(height: 14),
                currenciesAsync.when(
                  data: (data) {
                    final available = (data['available'] as List).map((e) => e.toString()).toList();
                    final def = (data['default'] ?? 'KSh').toString();
                    _currency ??= oldCurrency.isNotEmpty ? oldCurrency : def;

                    return DropdownButtonFormField<String>(
                      initialValue: _currency,
                      items: available.map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
                      onChanged: _saving
                          ? null
                          : (v) {
                              setState(() {
                                _currency = v;
                              });
                            },
                      decoration: const InputDecoration(labelText: 'Currency', prefixIcon: Icon(Icons.currency_exchange)),
                    );
                  },
                  loading: () => const SizedBox(
                    height: 56,
                    child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
                  ),
                  error: (e, _) => Text('Failed to load currencies: $e'),
                ),
                const SizedBox(height: 18),
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: FilledButton(
                    onPressed: _saving
                        ? null
                        : () async {
                            final newCurrency = _currency ?? oldCurrency;
                            if (newCurrency != oldCurrency) {
                              final ok = await _confirmCurrencyChange(context);
                              if (!ok) return;
                            }

                            setState(() => _saving = true);
                            try {
                              final result = await ref.read(profileActionsProvider).update(
                                    name: _nameCtrl.text.trim(),
                                    email: _emailCtrl.text.trim(),
                                    phone: _phoneCtrl.text.trim(),
                                    currency: newCurrency,
                                  );

                              if (!context.mounted) return;

                              final msg = result.cleared
                                  ? 'Profile updated. Data zote za zamani zimefutwa kwa sababu ya kubadilisha currency.'
                                  : 'Profile updated.';

                              ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
                            } catch (e) {
                              if (!context.mounted) return;
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(content: Text(e.toString().replaceFirst('Exception: ', ''))),
                              );
                            } finally {
                              if (mounted) setState(() => _saving = false);
                            }
                          },
                    child: _saving
                        ? const SizedBox(
                            height: 18,
                            width: 18,
                            child: CircularProgressIndicator(strokeWidth: 2),
                          )
                        : const Text('Hifadhi mabadiliko'),
                  ),
                ),
              ],
            );
          },
          loading: () => const Center(child: CircularProgressIndicator()),
          error: (e, _) => Center(child: Text('Failed to load profile: $e')),
        );

    if (widget.embedded) {
      return SafeArea(child: body);
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Profile'),
        actions: [
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
