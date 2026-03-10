@extends('layouts.user')

@section('page_title', 'Madeni na Mikopo')
@section('page_subtitle', 'Fuatilia miamala ya kukopa na kukopesha')

@section('content')
<div class="space-y-6">
    <!-- Debt Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2 text-gray-500 uppercase text-[10px] font-bold tracking-widest">
                <i class="fas fa-hand-holding-usd text-red-600"></i>
                <span>Nimekopwa (Madeni Yangu)</span>
            </div>
            <div class="text-2xl font-bold text-red-700">TSh {{ number_format($totalBorrowed, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Jumla ya pesa unazopaswa kulipa</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2 text-gray-500 uppercase text-[10px] font-bold tracking-widest">
                <i class="fas fa-hand-holding-heart text-emerald-600"></i>
                <span>Nimekopeshana (Madeni ya Nje)</span>
            </div>
            <div class="text-2xl font-bold text-emerald-700">TSh {{ number_format($totalLent, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Jumla ya pesa unazodai kwa wengine</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Debts List -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Orodha ya Madeni</h3>
                    <div class="flex gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-orange-100 text-orange-700 text-[10px] font-bold uppercase">Yanasubiri</span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase">Yalolipwa</span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100">
                                <th class="px-6 py-3">Mhusika</th>
                                <th class="px-6 py-3">Kiasi (TSh)</th>
                                <th class="px-6 py-3">Hali</th>
                                <th class="px-6 py-3">Muda wa Kurudisha</th>
                                <th class="px-6 py-3 text-right">Hatua</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @forelse($debts as $debt)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $debt->person_name }}</div>
                                        <div class="text-[10px] text-gray-500 uppercase">{{ $debt->type === 'borrowed' ? 'Nimekopa' : 'Nimekopeshana' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold {{ $debt->type === 'borrowed' ? 'text-red-700' : 'text-emerald-700' }}">
                                            {{ number_format($debt->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($debt->is_paid)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase">
                                                <i class="fas fa-check-circle text-[8px]"></i> Imelipwa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold uppercase">
                                                <i class="fas fa-clock text-[8px]"></i> Inasubiri
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600">
                                        @if($debt->due_date)
                                            <div class="{{ !$debt->is_paid && $debt->due_date->isPast() ? 'text-red-600 font-bold' : '' }}">
                                                {{ $debt->due_date->format('d/m/Y') }}
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                                        @if(!$debt->is_paid)
                                            <button onclick="openPaymentModal({{ $debt->id }}, '{{ $debt->person_name }}', {{ $debt->amount }})" 
                                                    class="text-emerald-600 hover:text-emerald-800 font-bold text-xs uppercase transition">
                                                Lipa Sasa
                                            </button>
                                        @else
                                            <i class="fas fa-check-double text-emerald-500"></i>
                                        @endif

                                        <form action="{{ route('debts.destroy', $debt) }}" method="POST" onsubmit="return confirm('Je, una uhakika unataka kufuta deni la {{ $debt->person_name }} la TSh {{ number_format($debt->amount, 0) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition p-2">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Hakuna madeni yaliyorekodiwa bado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Debt Form -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm sticky top-24">
                <h3 class="text-sm font-bold text-gray-900 uppercase mb-6 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-red-600"></i>
                    Rekodi Deni Jipya
                </h3>

                <form action="{{ route('debts.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Aina ya Deni</label>
                        <select name="type" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-600 focus:border-transparent outline-none transition">
                            <option value="borrowed">Nimekopa (Deni Langu)</option>
                            <option value="lent">Nimekopeshana (Nidai)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Mhusika (Jina)</label>
                        <input type="text" name="person_name" required placeholder="mfano: John Doe"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-600 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Kiasi (TSh)</label>
                        <input type="number" name="amount" required step="0.01" min="0" placeholder="0.00"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-600 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Tarehe ya Kukopa</label>
                        <input type="date" name="date" required value="{{ now()->toDateString() }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-600 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Tarehe ya Kurudisha</label>
                        <input type="date" name="due_date"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-600 focus:border-transparent outline-none transition">
                    </div>

                    <button type="submit" class="w-full bg-red-700 text-white font-bold py-3 rounded-xl hover:bg-red-800 transition shadow-md flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Hifadhi Deni</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl animate-in zoom-in duration-300">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-gray-900 uppercase text-sm">Rekodi Malipo</h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="payment-form" method="POST" class="p-6 space-y-5">
            @csrf
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Kulipa Deni kwa:</p>
                <div id="modal-person-name" class="text-lg font-bold text-gray-900">-</div>
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Kiasi unacholipa (TSh)</label>
                <input type="number" name="amount" id="modal-amount" required step="0.01" min="0"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Tarehe ya Malipo</label>
                <input type="date" name="payment_date" required value="{{ now()->toDateString() }}"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Ujumbe / Kumbukumbu (Si lazima)</label>
                <textarea name="note" rows="2" placeholder="mfano: Sehemu ya kwanza ya malipo..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition resize-none"></textarea>
            </div>

            <button type="submit" class="w-full bg-emerald-700 text-white font-bold py-3 rounded-xl hover:bg-emerald-800 transition shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>Thibitisha Malipo</span>
            </button>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(debtId, personName, amount) {
        const modal = document.getElementById('payment-modal');
        const form = document.getElementById('payment-form');
        const nameEl = document.getElementById('modal-person-name');
        const amountInput = document.getElementById('modal-amount');

        nameEl.textContent = personName;
        amountInput.value = amount;
        form.action = `/debts/${debtId}/pay`;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePaymentModal() {
        const modal = document.getElementById('payment-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closePaymentModal();
    });
</script>
@endsection

