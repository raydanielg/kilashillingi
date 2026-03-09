@extends('layouts.user')

@section('page_title', 'Ongeza Mapato')
@section('page_subtitle', 'Rekodi pesa iliyoingia')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <div class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">Tarehe</div>
                    <div class="mt-1 font-extrabold text-gray-900">{{ now('Africa/Dar_es_Salaam')->format('d/m/Y') }}</div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">Muda</div>
                    <div id="income-live-time" class="mt-1 font-extrabold text-gray-900">--:--:--</div>
                </div>
            </div>

            <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="type" value="income">
            <input type="hidden" name="date" value="{{ now('Africa/Dar_es_Salaam')->toDateString() }}">

            <div>
                <label class="block text-sm font-extrabold text-gray-900 mb-2">Kiasi cha pesa</label>
                <input name="amount" type="number" step="0.01" min="0" value="{{ old('amount') }}" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none">
                @error('amount')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-extrabold text-gray-900 mb-2">Chanzo cha mapato</label>
                <select id="income-source" name="source" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none">
                    <option value="">Chagua chanzo</option>
                    @foreach(['Mshahara','Biashara','Kibarua','Mkopeshaji','Zawadi','Nyingine'] as $src)
                        <option value="{{ $src }}" @selected(old('source') === $src)>{{ $src }}</option>
                    @endforeach
                    <option value="__custom__" @selected(old('source') === '__custom__')>Nyingine (ongeza yako)</option>
                </select>
            </div>

            <div id="income-source-custom" class="hidden">
                <label class="block text-sm font-extrabold text-gray-900 mb-2">Andika chanzo cha mapato</label>
                <input name="source_custom" type="text" value="{{ old('source_custom') }}" placeholder="mfano: Freelance / Mauzo ya Mazao"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none">
            </div>

            <div>
                <label class="block text-sm font-extrabold text-gray-900 mb-2">Maelezo</label>
                <input name="description" type="text" value="{{ old('description') }}" placeholder="mfano: Mauzo ya duka la rejareja"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none">
                @error('description')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-emerald-700 text-white px-6 py-3 rounded-xl font-extrabold hover:bg-emerald-800 transition">
                    Hifadhi
                </button>
                <a href="{{ route('dashboard') }}" class="text-sm font-extrabold text-gray-600 hover:underline">Rudi Nyumbani</a>
            </div>
        </form>
        </div>

        <div class="mt-6 bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <div class="font-extrabold text-gray-900">Mapato ya hivi karibuni</div>
                    <div class="text-sm text-gray-500">Miamala 10 ya mwisho</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-500 uppercase tracking-widest">Tarehe & Muda</th>
                            <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-500 uppercase tracking-widest">Chanzo</th>
                            <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-500 uppercase tracking-widest">Maelezo</th>
                            <th class="px-6 py-3 text-right text-xs font-extrabold text-gray-500 uppercase tracking-widest">Kiasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse(($recentIncome ?? collect()) as $tx)
                            @php
                                $desc = (string) ($tx->description ?? '');
                                $parts = explode(' - ', $desc, 2);
                                $source = count($parts) > 1 ? $parts[0] : 'Mapato';
                                $note = count($parts) > 1 ? $parts[1] : $desc;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ optional($tx->created_at)->timezone('Africa/Dar_es_Salaam')->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700">{{ $source }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $note !== '' ? $note : '-' }}</td>
                                <td class="px-6 py-4 text-right text-sm font-extrabold text-emerald-700">
                                    + TSh {{ number_format($tx->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">Hakuna mapato yaliyorekodiwa bado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-6 h-fit">
        <div class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">Kidokezo</div>
        <div class="mt-2 font-extrabold text-gray-900">Rekodi mapato yote</div>
        <div class="mt-2 text-sm text-gray-600">
            Rekodi kila pesa inayoingia ili kuweza kupanga bajeti yako kwa ufanisi zaidi.
        </div>
        <div class="mt-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-sm text-emerald-900">
            Kama chanzo chako cha mapato hakipo kwenye orodha, chagua “Nyingine” na uandike jina lake.
        </div>
    </div>
</div>

<script>
    (function () {
        const timeEl = document.getElementById('income-live-time');
        const sourceEl = document.getElementById('income-source');
        const customWrap = document.getElementById('income-source-custom');

        const tick = () => {
            if (!timeEl) return;
            const now = new Date();
            timeEl.textContent = now.toLocaleTimeString('sw-TZ', { hour12: false, timeZone: 'Africa/Dar_es_Salaam' });
        };

        const syncCustom = () => {
            if (!sourceEl || !customWrap) return;
            const isCustom = sourceEl.value === '__custom__';
            customWrap.classList.toggle('hidden', !isCustom);
        };

        tick();
        setInterval(tick, 1000);
        syncCustom();
        sourceEl?.addEventListener('change', syncCustom);
    })();
</script>
@endsection
