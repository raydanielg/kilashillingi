<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&display=swap" rel="stylesheet">

<div class="mx-auto max-w-2xl bg-white shadow-sm border border-gray-100 p-4 sm:p-8 font-serif text-gray-900 print:shadow-none print:border-none print:p-0 print:m-0 w-full" id="report-content">
    <!-- Header Sehemu ya Juu -->
    <div class="text-center border-b border-black/20 pb-3 mb-4">
        <div class="flex justify-center mb-2">
            <img src="{{ asset('xing_5968847.png') }}" alt="Logo" class="h-10 w-auto">
        </div>
        <h1 class="text-sm font-bold uppercase tracking-tight mb-0.5">JAMHURI YA MUUNGANO WA TANZANIA</h1>
        <h2 class="text-xs font-bold uppercase mb-1 text-gray-800">MFUMO WA KILASHILLINGI MANAGEMENT</h2>
        <div class="text-[9px] font-medium flex justify-center gap-4 text-gray-600 lowercase italic">
            <span>simu: {{ Auth::user()->phone ?? '0XXXXXXXXX' }}</span>
            <span>email: {{ Auth::user()->email }}</span>
        </div>
    </div>

    <!-- Kichwa cha Ripoti -->
    <div class="text-center mb-4">
        <h3 class="text-xs font-bold uppercase underline decoration-1 underline-offset-2">
            {{ $title }}
        </h3>
    </div>

    @if($type === 'week' && count($dailyReports) > 0)
        <!-- Weekly View: Grouped by Day -->
        @foreach($dailyReports as $day)
            <div class="mb-8 border-l-2 border-emerald-600 pl-4">
                <div class="flex items-center justify-between mb-2 bg-gray-50 p-1.5 border border-black/10">
                    <span class="text-[10px] font-extrabold uppercase"><i class="fas fa-calendar-day mr-1"></i> Tarehe: {{ $day['date'] }}</span>
                    <div class="flex gap-3 text-[9px] font-bold">
                        <span class="text-emerald-700">IN: {{ number_format($day['income'], 0) }}</span>
                        <span class="text-red-700">OUT: {{ number_format($day['expense'], 0) }}</span>
                    </div>
                </div>

                <div class="border border-black overflow-hidden bg-white mb-2">
                    <table class="w-full border-collapse text-[9px]">
                        <thead>
                            <tr class="bg-gray-100 border-b border-black">
                                <th class="border-r border-black p-1 text-left w-16">MUDA</th>
                                <th class="border-r border-black p-1 text-left w-12">AINA</th>
                                <th class="border-r border-black p-1 text-left">MAELEZO</th>
                                <th class="p-1 text-right w-24">KIASI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/10">
                            @foreach($day['transactions'] as $tx)
                                <tr>
                                    <td class="border-r border-black p-1 text-gray-500">{{ $tx->created_at->format('H:i') }}</td>
                                    <td class="border-r border-black p-1 font-bold {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-red-700' }}">
                                        {{ $tx->type === 'income' ? 'IN' : 'OUT' }}
                                    </td>
                                    <td class="border-r border-black p-1 italic text-gray-600 truncate max-w-[120px]">{{ $tx->description }}</td>
                                    <td class="p-1 text-right font-bold {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-red-700' }}">
                                        {{ number_format($tx->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-blue-50/30 p-2 border border-black/5">
                    <h4 class="text-[8px] font-bold uppercase text-gray-400 mb-0.5">Maoni ya siku:</h4>
                    <div class="text-sm font-bold text-blue-800 leading-tight" style="font-family: 'Caveat', cursive;">
                        "{{ $day['comment'] }}"
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Grand Total for Week -->
        <div class="mt-10 pt-4 border-t-2 border-double border-black">
            <h4 class="text-[10px] font-extrabold uppercase text-center mb-4">JUMLA KUU YA WIKI (SUMMARY)</h4>
            <div class="grid grid-cols-3 gap-0 border border-black bg-white text-center">
                <div class="border-r border-black p-2">
                    <div class="text-[8px] font-bold uppercase text-gray-400">Mapato Wiki</div>
                    <span class="text-xs font-bold text-emerald-700">TSh {{ number_format($totalIncome, 2) }}</span>
                </div>
                <div class="border-r border-black p-2">
                    <div class="text-[8px] font-bold uppercase text-gray-400">Matumizi Wiki</div>
                    <span class="text-xs font-bold text-red-700">TSh {{ number_format($totalExpense, 2) }}</span>
                </div>
                <div class="p-2 {{ $balance >= 0 ? 'bg-emerald-50/30' : 'bg-red-50/30' }}">
                    <div class="text-[8px] font-bold uppercase text-gray-400">Salio Wiki</div>
                    <span class="text-xs font-bold {{ $balance >= 0 ? 'text-emerald-800' : 'text-red-800' }}">TSh {{ number_format($balance, 2) }}</span>
                </div>
            </div>
        </div>

    @else
        <!-- Standard View (Today/Month) -->
        <div class="grid grid-cols-2 gap-0 border border-black mb-4 bg-gray-50/20">
            <div class="border-r border-black p-1.5">
                <span class="text-[8px] font-bold uppercase block text-gray-400">Mtumiaji:</span>
                <span class="text-[10px] font-bold uppercase">{{ Auth::user()->name }}</span>
            </div>
            <div class="p-1.5">
                <span class="text-[8px] font-bold uppercase block text-gray-400">Tarehe:</span>
                <span class="text-[10px] font-bold uppercase">{{ now('Africa/Dar_es_Salaam')->format('d/m/Y') }}</span>
            </div>
        </div>

        @if($transactions->isEmpty())
            <div class="py-8 text-center border border-black/10 bg-gray-50/50 mb-4">
                <p class="text-[9px] text-gray-500 italic font-bold uppercase">HAKUNA TAARIFA ZA MIAMALA.</p>
            </div>
        @else
            <div class="grid grid-cols-3 gap-0 border border-black mb-4 bg-white text-center">
                <div class="border-r border-black p-1.5">
                    <div class="text-[8px] font-bold uppercase text-gray-400">Mapato</div>
                    <span class="text-[10px] font-bold text-emerald-700">TSh {{ number_format($totalIncome, 2) }}</span>
                </div>
                <div class="border-r border-black p-1.5">
                    <div class="text-[8px] font-bold uppercase text-gray-400">Matumizi</div>
                    <span class="text-[10px] font-bold text-red-700">TSh {{ number_format($totalExpense, 2) }}</span>
                </div>
                <div class="p-1.5 {{ $balance >= 0 ? 'bg-emerald-50/30' : 'bg-red-50/30' }}">
                    <div class="text-[8px] font-bold uppercase text-gray-400">Salio</div>
                    <span class="text-[10px] font-bold {{ $balance >= 0 ? 'text-emerald-800' : 'text-red-800' }}">TSh {{ number_format($balance, 2) }}</span>
                </div>
            </div>

            <div class="border border-black mb-4 overflow-hidden bg-white">
                <table class="w-full border-collapse text-[9px]">
                    <thead>
                        <tr class="bg-gray-100 border-b border-black">
                            <th class="border-r border-black p-1 text-left w-16">MUDA</th>
                            <th class="border-r border-black p-1 text-left w-12">AINA</th>
                            <th class="border-r border-black p-1 text-left">MAELEZO</th>
                            <th class="p-1 text-right w-24">KIASI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/10">
                        @foreach($transactions as $tx)
                            <tr>
                                <td class="border-r border-black p-1 text-gray-500">{{ $tx->created_at->format('H:i') }}</td>
                                <td class="border-r border-black p-1 font-bold {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $tx->type === 'income' ? 'IN' : 'OUT' }}
                                </td>
                                <td class="border-r border-black p-1 italic text-gray-600 truncate max-w-[120px]">{{ $tx->description }}</td>
                                <td class="p-1 text-right font-bold {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ number_format($tx->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="border border-black p-2 bg-white">
            <h4 class="text-[8px] font-bold uppercase text-gray-400 mb-0.5 underline">Maoni:</h4>
            <div class="text-base font-bold text-blue-800 leading-tight" style="font-family: 'Caveat', cursive;">
                "{{ $comment }}"
            </div>
        </div>
    @endif
</div>

<!-- Floating Action Button for Printing -->
<button onclick="window.print()" class="fixed bottom-6 right-6 w-14 h-14 bg-black text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition z-[60] no-print group">
    <i class="fas fa-print text-xl"></i>
    <span class="absolute right-16 bg-black text-white px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Print Ripoti</span>
</button>

<style>
    @media print {
        nav, aside, header, footer, .no-print, #sidebar-overlay, #open-sidebar, #profile-dropdown-wrapper {
            display: none !important;
        }
        body, main, .main-content {
            padding: 0 !important;
            margin: 0 !important;
            background: white !important;
        }
        #report-content {
            border: none !important;
            box-shadow: none !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .font-serif { font-family: serif !important; }
    }
</style>
