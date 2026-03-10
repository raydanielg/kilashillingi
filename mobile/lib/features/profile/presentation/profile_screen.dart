import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../auth/presentation/currency_provider.dart';
import 'profile_controller.dart';

class ProfileScreen extends ConsumerStatefulWidget {
  const ProfileScreen({super.key});

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
      body: SafeArea(
        child: profileAsync.when(
          data: (user) {
            _nameCtrl.text = _nameCtrl.text.isEmpty ? (user['name'] ?? '').toString() : _nameCtrl.text;
            _emailCtrl.text = _emailCtrl.text.isEmpty ? (user['email'] ?? '').toString() : _emailCtrl.text;
            _phoneCtrl.text = _phoneCtrl.text.isEmpty ? (user['phone'] ?? '').toString() : _phoneCtrl.text;
            _currency ??= (user['currency'] ?? 'KSh').toString();

            final oldCurrency = (user['currency'] ?? 'KSh').toString();

            return ListView(
              padding: const EdgeInsets.all(20),
              children: [
                TextField(
                  controller: _nameCtrl,
                  decoration: const InputDecoration(labelText: 'Jina', border: OutlineInputBorder()),
                ),
                const SizedBox(height: 14),
                TextField(
                  controller: _emailCtrl,
                  keyboardType: TextInputType.emailAddress,
                  decoration: const InputDecoration(labelText: 'Email', border: OutlineInputBorder()),
                ),
                const SizedBox(height: 14),
                TextField(
                  controller: _phoneCtrl,
                  keyboardType: TextInputType.phone,
                  decoration: const InputDecoration(labelText: 'Simu (mf 07XXXXXXXX)', border: OutlineInputBorder()),
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
                      decoration: const InputDecoration(labelText: 'Currency', border: OutlineInputBorder()),
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
                        : const Text('Hifadhi'),
                  ),
                ),
              ],
            );
          },
          loading: () => const Center(child: CircularProgressIndicator()),
          error: (e, _) => Center(child: Text('Failed to load profile: $e')),
        ),
      ),
    );
  }
}
