@extends('layouts.modern-auth')

@php($showSocial = false)

@section('auth_header')
<div class="mb-8">
    <div class="flex items-center gap-2 mb-6">
        <img src="{{ asset('xing_5968847.png') }}" alt="{{ config('app.name', 'KilaShillingi') }}" class="w-10 h-10 object-contain">
        <span class="text-2xl font-bold text-[#111827]">KilaShillingi</span>
    </div>
    <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">KARIBU</p>
    <h1 class="text-3xl font-bold text-[#111827] mb-2">Fungua akaunti mpya</h1>
    <p class="text-gray-500 text-sm">Anza safari yako ya usimamizi wa fedha leo na KilaShillingi.</p>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('register') }}" class="space-y-6">
    @csrf

    <!-- Name -->
    <div>
        <label for="name" class="block text-sm font-bold text-[#111827] mb-2 uppercase tracking-wide">
            {{ trans('messages.name') }}
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-regular fa-user text-gray-400"></i>
            </div>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Jina lako kamili"
                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition text-gray-900 @error('name') border-red-500 @enderror">
        </div>
        @error('name')
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Email Address -->
    <div>
        <label for="email" class="block text-sm font-bold text-[#111827] mb-2 uppercase tracking-wide">
            {{ trans('messages.email') }}
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-regular fa-envelope text-gray-400"></i>
            </div>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   placeholder="jina@mfano.com"
                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition text-gray-900 @error('email') border-red-500 @enderror">
        </div>
        @error('email')
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Phone -->
    <div>
        <label for="phone" class="block text-sm font-bold text-[#111827] mb-2 uppercase tracking-wide">
            {{ trans('messages.phone') }}
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-phone text-gray-400"></i>
            </div>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required autocomplete="tel"
                   placeholder="07XXXXXXXX"
                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition text-gray-900 @error('phone') border-red-500 @enderror">
        </div>
        @error('phone')
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Currency Selection -->
    <div>
        <label for="currency" class="block text-sm font-bold text-[#111827] mb-2 uppercase tracking-wide">
            ALAMA YA FEDHA (CURRENCY)
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-coins text-gray-400"></i>
            </div>
            <select id="currency" name="currency" required
                    class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition text-gray-900">
                @php
                    $availableCurrencies = explode(',', \App\Models\Setting::get('available_currencies', 'TSh,KSh,USh,$,€,£'));
                    $defaultCurrency = \App\Models\Setting::get('currency', 'KSh');
                @endphp
                @foreach($availableCurrencies as $cur)
                    @php $cur = trim($cur); @endphp
                    <option value="{{ $cur }}" {{ old('currency', $defaultCurrency) == $cur ? 'selected' : '' }}>{{ $cur }}</option>
                @endforeach
            </select>
        </div>
        @error('currency')
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-sm font-bold text-[#111827] mb-2 uppercase tracking-wide">
            {{ trans('messages.password') }}
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-lock text-gray-400"></i>
            </div>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   placeholder="••••••••"
                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition text-gray-900 @error('password') border-red-500 @enderror">
        </div>
        @error('password')
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div>
        <label for="password_confirmation" class="block text-sm font-bold text-[#111827] mb-2 uppercase tracking-wide">
            {{ trans('messages.confirm_password') }}
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-lock text-gray-400"></i>
            </div>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="••••••••"
                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition text-gray-900 @error('password_confirmation') border-red-500 @enderror">
        </div>
        @error('password_confirmation')
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full bg-emerald-700 text-white py-4 px-6 rounded-xl font-extrabold text-sm uppercase tracking-widest hover:bg-emerald-800 active:scale-[0.98] transition shadow-lg shadow-emerald-900/10">
        {{ trans('messages.register') }}
    </button>
</form>
@endsection

@section('auth_footer')
<div class="mt-8 text-center">
    <p class="text-gray-600 text-sm">
        {{ trans('messages.already_registered') }}
        <a href="{{ route('login') }}" class="text-emerald-700 font-extrabold hover:underline">{{ trans('messages.login') }}</a>
    </p>
</div>
@endsection

