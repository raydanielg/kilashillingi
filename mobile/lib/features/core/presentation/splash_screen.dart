import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../auth/presentation/auth_controller.dart';
import '../../auth/presentation/auth_state.dart';

class SplashScreen extends ConsumerStatefulWidget {
  const SplashScreen({super.key});

  @override
  ConsumerState<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends ConsumerState<SplashScreen> {
  @override
  void initState() {
    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(authStateProvider.notifier).bootstrap();
    });
  }

  @override
  Widget build(BuildContext context) {
    ref.listen<AuthState>(authStateProvider, (previous, next) {
      if (!mounted) return;

      if (next.status == AuthStatus.authenticated) {
        context.go('/dashboard');
      } else if (next.status == AuthStatus.unauthenticated) {
        context.go('/login');
      }
    });

    return const Scaffold(
      body: Center(
        child: CircularProgressIndicator(),
      ),
    );
  }
}
