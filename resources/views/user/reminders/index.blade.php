@extends('layouts.user')

@section('page_title', 'Kumbusho')
@section('page_subtitle', 'Simamia vikumbusho vyako vya malipo na miamala')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2 text-gray-500 uppercase text-[10px] font-bold tracking-widest">
                <i class="fas fa-bell text-emerald-600"></i>
                <span>Vikumbusho Hai</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $reminders->where('is_active', true)->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Jumla ya vikumbusho vinavyofanya kazi</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2 text-gray-500 uppercase text-[10px] font-bold tracking-widest">
                <i class="fas fa-bell-slash text-gray-400"></i>
                <span>Zimezimwa</span>
            </div>
            <div class="text-2xl font-bold text-gray-500">{{ $reminders->where('is_active', false)->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Vikumbusho ambavyo havifanyi kazi</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2 text-gray-500 uppercase text-[10px] font-bold tracking-widest">
                <i class="fas fa-clock text-blue-600"></i>
                <span>Kumbusho Inayofuata</span>
            </div>
            @php 
                $next = $reminders->where('is_active', true)->first(); 
            @endphp
            <div class="text-2xl font-bold text-blue-700">{{ $next ? \Carbon\Carbon::parse($next->reminder_time)->format('H:i') : '--:--' }}</div>
            <p class="text-xs text-gray-500 mt-1">{{ $next ? $next->title : 'Hakuna kumbusho lililopangwa' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Reminders List -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Orodha ya Vikumbusho</h3>
                </div>
                
                <div class="divide-y divide-gray-50">
                    @forelse($reminders as $reminder)
                        <div class="p-6 flex items-center justify-between hover:bg-gray-50/50 transition">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl {{ $reminder->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shrink-0">
                                    <i class="fas {{ $reminder->is_active ? 'fa-bell' : 'fa-bell-slash' }}"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold {{ $reminder->is_active ? 'text-gray-900' : 'text-gray-500' }}">{{ $reminder->title }}</h4>
                                    <div class="flex items-center gap-3 mt-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                                        <span><i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('H:i') }}</span>
                                        <span>•</span>
                                        <span><i class="fas fa-redo mr-1"></i> {{ $reminder->frequency === 'daily' ? 'Kila Siku' : ($reminder->frequency === 'weekly' ? 'Kila Wiki' : 'Kila Mwezi') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <form action="{{ route('reminders.toggle', $reminder) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-10 h-6 rounded-full transition-colors relative {{ $reminder->is_active ? 'bg-emerald-600' : 'bg-gray-300' }}">
                                        <div class="absolute top-1 left-1 bg-white w-4 h-4 rounded-full transition-transform {{ $reminder->is_active ? 'translate-x-4' : '' }}"></div>
                                    </button>
                                </form>
                                <form action="{{ route('reminders.destroy', $reminder) }}" method="POST" onsubmit="return confirm('Je, una uhakika unataka kufuta kumbusho la \'{{ $reminder->title }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition p-2">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16">
                            <div class="text-4xl mb-3 text-gray-200"><i class="fas fa-bell-slash"></i></div>
                            <p class="text-sm text-gray-500">Hujapanga kumbusho lolote bado.</p>
                            <p class="text-xs text-gray-400 mt-1">Anza kwa kujaza fomu ya upande wa kulia.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Add Reminder Form -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm sticky top-24">
                <h3 class="text-sm font-bold text-gray-900 uppercase mb-6 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-emerald-600"></i>
                    Weka Kumbusho Jipya
                </h3>

                <form action="{{ route('reminders.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Kichwa cha Kumbusho</label>
                        <input type="text" name="title" required placeholder="mfano: Lipa Kodi ya Nyumba"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Muda</label>
                            <input type="time" name="reminder_time" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Mzunguko</label>
                            <select name="frequency" id="reminder-frequency" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition">
                                <option value="daily">Kila Siku</option>
                                <option value="weekly">Kila Wiki</option>
                                <option value="monthly">Kila Mwezi</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-700 text-white font-bold py-3 rounded-xl hover:bg-emerald-800 transition shadow-md flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Hifadhi Kumbusho</span>
                    </button>
                </form>

                <div class="mt-8 p-4 bg-orange-50 rounded-xl border border-orange-100">
                    <div class="flex gap-3">
                        <i class="fas fa-lightbulb text-orange-600 mt-1"></i>
                        <div>
                            <h4 class="text-xs font-bold text-orange-900 uppercase mb-1">Kidokezo</h4>
                            <p class="text-[11px] text-orange-800 leading-relaxed">
                                Hakikisha umeruhusu 'Notifications' kwenye browser yako ili uweze kupata taarifa kwa wakati.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
