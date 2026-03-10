@extends('layouts.user')

@section('page_title', 'Bajeti')
@section('page_subtitle', 'Panga na ufuatilie matumizi yako kwa kila kundi')

@section('content')
<div class="space-y-6">
    <!-- Budget Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2 text-gray-500 uppercase text-[10px] font-bold tracking-widest">
                <i class="fas fa-wallet text-emerald-600"></i>
                <span>Jumla ya Bajeti</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ Auth::user()->currency ?? 'TSh' }} {{ number_format($totalBudgetLimit, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Kiwango ulichojiwekea mwezi huu</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2 text-gray-500 uppercase text-[10px] font-bold tracking-widest">
                <i class="fas fa-shopping-cart text-red-600"></i>
                <span>Zilizotumika</span>
            </div>
            <div class="text-2xl font-bold text-red-700">{{ Auth::user()->currency ?? 'TSh' }} {{ number_format($totalActualSpent, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Matumizi halisi hadi sasa</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            @php $remaining = $totalBudgetLimit - $totalActualSpent; @endphp
            <div class="flex items-center gap-3 mb-2 text-gray-500 uppercase text-[10px] font-bold tracking-widest">
                <i class="fas fa-piggy-bank text-blue-600"></i>
                <span>Salio la Bajeti</span>
            </div>
            <div class="text-2xl font-bold {{ $remaining >= 0 ? 'text-emerald-800' : 'text-red-800' }}">
                {{ Auth::user()->currency ?? 'TSh' }} {{ number_format(abs($remaining), 2) }}
                @if($remaining < 0) <span class="text-xs uppercase ml-1">(Zimezidi)</span> @endif
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ $remaining >= 0 ? 'Kiasi kilichobaki kutumika' : 'Umezidisha bajeti yako' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Budget List & Progress -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Mfuatilio wa Bajeti kwa Makundi</h3>
                    <span class="text-[10px] font-bold text-gray-500 uppercase">{{ now('Africa/Dar_es_Salaam')->format('F Y') }}</span>
                </div>
                
                <div class="p-6 space-y-6">
                    @forelse($budgetData as $item)
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <span class="text-sm font-bold text-gray-900">{{ $item['category'] }}</span>
                                    <div class="text-[10px] text-gray-500 uppercase">
                                        {{ number_format($item['spent'], 0) }} / {{ number_format($item['limit'], 0) }} {{ Auth::user()->currency ?? 'TSh' }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold {{ $item['is_over'] ? 'text-red-600' : 'text-emerald-700' }}">
                                        {{ number_format($item['percent'], 1) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full transition-all duration-500 {{ $item['is_over'] ? 'bg-red-600' : ($item['percent'] > 80 ? 'bg-orange-500' : 'bg-emerald-600') }}" 
                                     style="width: {{ $item['percent'] }}%"></div>
                            </div>
                            @if($item['is_over'])
                                <p class="text-[10px] text-red-600 mt-1 font-bold italic">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Umezidisha bajeti ya {{ $item['category'] }} kwa {{ Auth::user()->currency ?? 'TSh' }} {{ number_format($item['spent'] - $item['limit'], 0) }}
                                </p>
                            @endif
                            @if(isset($item['id']))
                            <div class="mt-2 flex justify-end">
                                <form action="{{ route('budget.destroy', $item['id']) }}" method="POST" onsubmit="return confirm('Je, una uhakika unataka kufuta bajeti ya {{ $item['category'] }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] text-red-600 hover:text-red-800 font-bold uppercase tracking-wider transition">
                                        <i class="fas fa-trash-alt mr-1"></i> Futa Bajeti
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="text-4xl mb-3">📉</div>
                            <p class="text-sm text-gray-500">Hujapanga bajeti yoyote mwezi huu.</p>
                            <p class="text-xs text-gray-400 mt-1">Tumia fomu ya kulia kuanza kupanga.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Add/Update Budget Form -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm sticky top-24">
                <h3 class="text-sm font-bold text-gray-900 uppercase mb-6 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-emerald-600"></i>
                    Weka/Badili Bajeti
                </h3>

                <form action="{{ route('budget.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Aina ya Matumizi</label>
                        <select name="category" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition">
                            <option value="">Chagua Kundi</option>
                            @foreach($knownCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Kiwango cha Juu (Limit)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">{{ Auth::user()->currency ?? 'TSh' }}</span>
                            <input type="number" name="amount" required step="0.01" min="0" placeholder="0.00"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-12 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-700 text-white font-bold py-3 rounded-xl hover:bg-emerald-800 transition shadow-md flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Hifadhi Bajeti</span>
                    </button>
                </form>

                <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="flex gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <div>
                            <h4 class="text-xs font-bold text-blue-900 uppercase mb-1">Kidokezo</h4>
                            <p class="text-[11px] text-blue-800 leading-relaxed">
                                Kupanga bajeti hukusaidia kudhibiti matumizi na kuokoa pesa kwa ajili ya malengo yako ya baadaye.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
