@extends('user.settings.layout')

@section('settings_content')
<section id="profile" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden animate-in fade-in duration-500">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
        <i class="fas fa-user-circle text-emerald-600"></i>
        <h3 class="text-sm font-bold text-gray-900 uppercase">Taarifa Binafsi</h3>
    </div>
    <div class="p-6 space-y-6">
        <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100">
            <div class="w-24 h-24 rounded-2xl bg-emerald-700 flex items-center justify-center text-white text-3xl font-bold shadow-lg overflow-hidden shrink-0">
                @if(Auth::user()->avatar)
                    <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    {{ substr(Auth::user()->name, 0, 1) }}
                @endif
            </div>
            <div class="text-center sm:text-left space-y-1">
                <h4 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h4>
                <p class="text-sm text-gray-500 lowercase">{{ Auth::user()->email }}</p>
                <div class="pt-2">
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Akaunti ya Mtumiaji</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jina Kamili</label>
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Barua Pepe</label>
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-900">{{ Auth::user()->email }}</div>
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Namba ya Simu</label>
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-900">{{ Auth::user()->phone ?? 'Hujatolewa' }}</div>
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Muda wa Kujiunga</label>
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-900">{{ Auth::user()->created_at->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="pt-4 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('profile.edit') }}" class="flex-1 bg-emerald-700 text-white font-bold py-3 rounded-xl hover:bg-emerald-800 transition flex items-center justify-center gap-2 text-sm shadow-md">
                <i class="fas fa-user-edit text-xs"></i>
                <span>Hariri Profaili</span>
            </a>
            <button class="flex-1 bg-white border border-gray-200 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-camera text-xs"></i>
                <span>Badili Picha</span>
            </button>
        </div>
    </div>
</section>
@endsection
