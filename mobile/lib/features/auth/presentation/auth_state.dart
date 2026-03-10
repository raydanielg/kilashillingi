enum AuthStatus {
  unknown,
  unauthenticated,
  authenticated,
}

class AuthState {
  const AuthState({
    required this.status,
    this.isLoading = false,
    this.error,
    this.user,
  });

  final AuthStatus status;
  final bool isLoading;
  final String? error;
  final Map<String, dynamic>? user;

  AuthState copyWith({
    AuthStatus? status,
    bool? isLoading,
    String? error,
    Map<String, dynamic>? user,
  }) {
    return AuthState(
      status: status ?? this.status,
      isLoading: isLoading ?? this.isLoading,
      error: error,
      user: user ?? this.user,
    );
  }

  static const unknown = AuthState(status: AuthStatus.unknown);
  static const unauthenticated = AuthState(status: AuthStatus.unauthenticated);
}
