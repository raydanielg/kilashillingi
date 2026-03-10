import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../features/auth/presentation/login_screen.dart';
import '../features/auth/presentation/register_screen.dart';
import '../features/core/presentation/dashboard_screen.dart';
import '../features/core/presentation/splash_screen.dart';
import '../features/profile/presentation/profile_screen.dart';
import '../features/transactions/presentation/transactions_screen.dart';
import '../features/budgets/presentation/budgets_screen.dart';

final appRouterProvider = Provider<GoRouter>((ref) {
  return GoRouter(
    initialLocation: '/',
    routes: [
      GoRoute(
        path: '/',
        builder: (context, state) => const SplashScreen(),
      ),
      GoRoute(
        path: '/login',
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: '/register',
        builder: (context, state) => const RegisterScreen(),
      ),
      GoRoute(
        path: '/dashboard',
        builder: (context, state) => const DashboardScreen(),
      ),
      GoRoute(
        path: '/profile',
        builder: (context, state) => const ProfileScreen(),
      ),
      GoRoute(
        path: '/transactions',
        builder: (context, state) => const TransactionsScreen(),
      ),
      GoRoute(
        path: '/budgets',
        builder: (context, state) => const BudgetsScreen(),
      ),
    ],
  );
});
