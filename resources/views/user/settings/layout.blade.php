@extends('layouts.user')

@section('page_title', 'Mipangilio')
@section('page_subtitle', 'Simamia akaunti yako na mapendeleo ya mfumo')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar ya Mipangilio -->
        <div class="md:col-span-1 space-y-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-2 shadow-sm sticky top-24">
                <nav class="space-y-1">
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('settings.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }} transition">
                        <i class="fas fa-user-circle w-5"></i>
                        <span>Taarifa Binafsi</span>
                    </a>
                    <a href="{{ route('settings.security') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('settings.security') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }} transition">
                        <i class="fas fa-shield-alt w-5"></i>
                        <span>Usalama</span>
                    </a>
                    <a href="{{ route('settings.preferences') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('settings.preferences') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }} transition">
                        <i class="fas fa-sliders-h w-5"></i>
                        <span>Mapendeleo</span>
                    </a>
                    <a href="{{ route('settings.instructions') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('settings.instructions') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }} transition">
                        <i class="fas fa-book w-5"></i>
                        <span>Maelezo ya Matumizi</span>
                    </a>
                    <a href="{{ route('settings.legal') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('settings.legal') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-600 hover:bg-gray-50' }} transition">
                        <i class="fas fa-gavel w-5"></i>
                        <span>Sheria na Masharti</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Sehemu Kuu -->
        <div class="md:col-span-2">
            @yield('settings_content')
        </div>
    </div>
</div>
@endsection
