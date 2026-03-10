import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../data/auth_repository.dart';
import '../../../core/network/dio_provider.dart';

final forgotPasswordProvider = Provider<ForgotPasswordActions>((ref) {
  return ForgotPasswordActions(AuthRepository(ref.watch(dioProvider)));
});

class ForgotPasswordActions {
  ForgotPasswordActions(this._repo);

  final AuthRepository _repo;

  Future<void> send(String email) => _repo.forgotPassword(email: email);
}

class ForgotPasswordScreen extends ConsumerStatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  ConsumerState<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends ConsumerState<ForgotPasswordScreen> {
  final _emailCtrl = TextEditingController();
  bool _loading = false;
  String? _error;

  @override
  void dispose() {
    _emailCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Forgot Password'),
        leading: IconButton(
          onPressed: () => context.pop(),
          icon: const Icon(Icons.arrow_back_ios_new),
        ),
      ),
      body: SafeArea(
        child: ListView(
          padding: EdgeInsets.fromLTRB(20, 20, 20, 20 + MediaQuery.of(context).viewInsets.bottom),
          children: [
            Row(
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: Image.asset('assets/app_icon.png', width: 44, height: 44, fit: BoxFit.cover),
                ),
                const SizedBox(width: 12),
                Text(
                  'KilaShillingi',
                  style: theme.textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900),
                ),
              ],
            ),
            const SizedBox(height: 18),
            Text(
              'Reset password',
              style: theme.textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w900),
            ),
            const SizedBox(height: 6),
            Text(
              'Weka email yako. Tutakutumia link ya kubadilisha password.',
              style: theme.textTheme.bodyMedium?.copyWith(color: Colors.black54),
            ),
            if (_error != null) ...[
              const SizedBox(height: 14),
              Material(
                color: Colors.red.withValues(alpha: 0.08),
                borderRadius: BorderRadius.circular(12),
                child: Padding(
                  padding: const EdgeInsets.all(12),
                  child: Text(
                    _error!,
                    style: theme.textTheme.bodyMedium?.copyWith(color: Colors.red.shade800),
                  ),
                ),
              ),
            ],
            const SizedBox(height: 18),
            TextField(
              controller: _emailCtrl,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(labelText: 'Email'),
            ),
            const SizedBox(height: 16),
            FilledButton(
              onPressed: _loading
                  ? null
                  : () async {
                      setState(() {
                        _loading = true;
                        _error = null;
                      });

                      try {
                        await ref.read(forgotPasswordProvider).send(_emailCtrl.text.trim());
                        if (!context.mounted) return;
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Reset link imetumwa. Kagua email yako.')),
                        );
                        context.pop();
                      } catch (e) {
                        setState(() {
                          _error = e.toString().replaceFirst('Exception: ', '');
                        });
                      } finally {
                        if (mounted) {
                          setState(() => _loading = false);
                        }
                      }
                    },
              child: _loading
                  ? const SizedBox(
                      height: 18,
                      width: 18,
                      child: CircularProgressIndicator(strokeWidth: 2),
                    )
                  : const Text('Tuma link'),
            ),
            const SizedBox(height: 14),
            Center(
              child: TextButton(
                onPressed: () => context.pop(),
                child: const Text('Rudi Login'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
