@extends('layouts.modern-auth')

@php($showSocial = false)

@section('auth_header')
<div class="mb-8">
    <div class="flex items-center gap-2 mb-6">
        <img src="{{ asset('xing_5968847.png') }}" alt="{{ config('app.name', 'KilaShillingi') }}" class="w-10 h-10 object-contain">
        <span class="text-2xl font-bold text-[#111827]">KilaShillingi</span>
    </div>
    <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-2">USAIDIZI</p>
    <h1 class="text-3xl font-bold text-[#111827] mb-2">{{ trans('messages.forgot_password') }}</h1>
    <p class="text-gray-500 text-sm">{{ trans('messages.forgot_password_instruction') }}</p>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="space-y-6">
    @csrf

    <!-- Email Address -->
    <div>
        <label for="email" class="block text-sm font-bold text-[#111827] mb-2 uppercase tracking-wide">
            {{ trans('messages.email') }}
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-regular fa-envelope text-gray-400"></i>
            </div>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="jina@mfano.com"
                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition text-gray-900 @error('email') border-red-500 @enderror">
        </div>
        @error('email')
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full bg-emerald-700 text-white py-4 px-6 rounded-xl font-extrabold text-sm uppercase tracking-widest hover:bg-emerald-800 active:scale-[0.98] transition shadow-lg shadow-emerald-900/10">
        {{ trans('messages.send_reset_link') }}
    </button>
</form>
@endsection

@section('auth_footer')
<div class="mt-8 text-center">
    <p class="text-gray-600 text-sm">
        Unakumbuka nywila?
        <a href="{{ route('login') }}" class="text-emerald-700 font-extrabold hover:underline">Rudi kuingia</a>
    </p>
</div>
@endsection


