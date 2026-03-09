@extends('layouts.modern-auth')

@section('auth_header')
<div class="mb-8">
    <div class="flex items-center gap-2 mb-6">
        <img src="{{ asset('xing_5968847.png') }}" alt="{{ config('app.name', 'KilaShillingi') }}" class="w-10 h-10 object-contain">
        <span class="text-2xl font-bold text-gray-900">KilaShillingi</span>
    </div>
    <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">KARIBU TENA</p>
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Ingia kwenye akaunti</h1>
    <p class="text-gray-600 text-sm">Tumia rekodi zako za KilaShillingi kusimamia mapato na matumizi yako.</p>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-6">
    @csrf

    <!-- Email or Phone -->
    <div>
        <label for="login" class="block text-sm font-bold text-[#111827] mb-2 uppercase tracking-wide">
            {{ trans('messages.email') }} / {{ trans('messages.phone') }}
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-regular fa-user text-gray-400"></i>
            </div>
            <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                   placeholder="jina@mfano.com au 07XXXXXXXX"
                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition text-gray-900 @error('login') border-red-500 @enderror">
        </div>
        @error('login')
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <div class="flex justify-between items-center mb-2">
            <label for="password" class="block text-sm font-bold text-[#111827] uppercase tracking-wide">
                {{ trans('messages.password') }}
            </label>
        </div>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-lock text-gray-400"></i>
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="••••••••"
                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition text-gray-900 @error('password') border-red-500 @enderror">
        </div>
        @error('password')
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="flex items-center justify-between">
        <label for="remember_me" class="inline-flex items-center group cursor-pointer">
            <input id="remember_me" type="checkbox" name="remember" class="w-5 h-5 rounded border-gray-300 text-emerald-700 focus:ring-emerald-600 transition cursor-pointer">
            <span class="ms-2 text-sm text-gray-600 font-medium group-hover:text-gray-900 transition">{{ trans('messages.remember_me') }}</span>
        </label>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm font-extrabold text-red-600 hover:underline">
                {{ trans('messages.forgot_password') }}
            </a>
        @endif
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full bg-emerald-700 text-white py-4 px-6 rounded-xl font-extrabold text-sm uppercase tracking-widest hover:bg-emerald-800 active:scale-[0.98] transition shadow-lg shadow-emerald-900/10">
        {{ trans('messages.login') }}
    </button>
</form>
@endsection

@section('auth_footer')
<div class="mt-8 text-center">
    <p class="text-gray-600 text-sm">
        Huna akaunti?
        <a href="{{ route('register') }}" class="text-emerald-700 font-extrabold hover:underline">Jisajili sasa</a>
    </p>
</div>
@endsection
