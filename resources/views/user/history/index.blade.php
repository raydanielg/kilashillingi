@extends('layouts.user')

@section('page_title', 'Historia ya Miamala')
@section('page_subtitle', 'Tazama na utafute miamala yako yote')

@section('content')
<div class="space-y-6">
    <!-- Filters Card -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('history.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-1">
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Tafuta</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Maelezo..." 
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Kuanzia</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Mpaka</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-emerald-700 text-white font-bold py-2.5 rounded-xl hover:bg-emerald-800 transition flex items-center justify-center gap-2 text-sm shadow-sm">
                    <i class="fas fa-filter text-xs"></i>
                    <span>Chuja</span>
                </button>
                <a href="{{ route('history.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition flex items-center justify-center shadow-sm">
                    <i class="fas fa-undo text-xs"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 uppercase">Orodha ya Miamala</h3>
            <span class="text-[10px] font-bold text-gray-500 uppercase">Inaonyesha 50 kwa kila ukurasa</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">
                        <th class="px-6 py-3">Tarehe & Muda</th>
                        <th class="px-6 py-3">Maelezo</th>
                        <th class="px-6 py-3">Aina</th>
                        <th class="px-6 py-3 text-right">Kiasi (TSh)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($tx->date)->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-gray-400">{{ $tx->created_at->timezone('Africa/Dar_es_Salaam')->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900">{{ $tx->description }}</div>
                            </td>
                            <td class="px-6 py-4 uppercase text-[10px] font-bold">
                                @if($tx->type === 'income')
                                    <span class="text-emerald-700 bg-emerald-50 px-2 py-1 rounded-md">
                                        <i class="fas fa-arrow-down mr-1"></i>Mapato
                                    </span>
                                @else
                                    <span class="text-red-700 bg-red-50 px-2 py-1 rounded-md">
                                        <i class="fas fa-arrow-up mr-1"></i>Matumizi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }} {{ number_format($tx->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">Hakuna miamala kwa sasa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
