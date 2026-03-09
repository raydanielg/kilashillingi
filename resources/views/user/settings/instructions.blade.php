@extends('user.settings.layout')

@section('settings_content')
<section id="instructions" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden animate-in fade-in duration-500">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
        <i class="fas fa-book text-emerald-600"></i>
        <h3 class="text-sm font-bold text-gray-900 uppercase">Maelezo ya Matumizi</h3>
    </div>
    <div class="p-6 space-y-8">
        <!-- Dashboard Section -->
        <div class="flex gap-4">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <div class="space-y-2">
                <h4 class="text-md font-bold text-gray-900">1. Kuelewa Dashibodi</h4>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Dashibodi ndio kitovu cha mfumo wako. Hapa unaweza kuona muhtasari wa salio lako la sasa, mapato na matumizi ya mwezi, na grafu zinazoonyesha mwenendo wako wa kifedha. Hakikisha unapitia sehemu hii kila siku.
                </p>
            </div>
        </div>

        <!-- Transactions Section -->
        <div class="flex gap-4">
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="space-y-2">
                <h4 class="text-md font-bold text-gray-900">2. Kurekodi Miamala</h4>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Ili kupata ripoti sahihi, ni muhimu kurekodi kila mapato na matumizi mara yanapotokea. Tumia sehemu ya "Ongeza Mapato" kwa pesa zinazoingia na "Ongeza Matumizi" kwa pesa zinazotoka. Unaweza pia kuongeza kategoria mpya kama unavyohitaji.
                </p>
            </div>
        </div>

        <!-- Budgeting Section -->
        <div class="flex gap-4">
            <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center shrink-0">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="space-y-2">
                <h4 class="text-md font-bold text-gray-900">3. Kupanga Bajeti</h4>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Kwenye ukurasa wa "Bajeti", weka kiwango cha juu cha matumizi kwa kila kategoria (mfano: Chakula). Mfumo utakuonyesha asilimia ya matumizi yako kwa kila kundi na kutoa tahadhari ya rangi nyekundu ukizidisha bajeti.
                </p>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="flex gap-4">
            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="space-y-2">
                <h4 class="text-md font-bold text-gray-900">4. Kuchambua Ripoti</h4>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Tumia ukurasa wa "Ripoti" kutengeneza ripoti za Leo, Wiki au Mwezi. Ripoti hizi zimetengenezwa kwa muundo rasmi na zinajumuisha maoni ya kiotomatiki kutoka kwa mfumo kulingana na mwenendo wako wa fedha.
                </p>
            </div>
        </div>

        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
            <div class="flex gap-3">
                <i class="fas fa-lightbulb text-emerald-600 mt-1"></i>
                <div>
                    <h4 class="text-xs font-bold text-emerald-900 uppercase mb-1">Kidokezo cha Mafanikio</h4>
                    <p class="text-[11px] text-emerald-800 leading-relaxed">
                        Nidhamu ya kurekodi kila mmuamala mdogo (shillingi kwa shillingi) ndio siri pekee ya kufikia malengo yako makubwa ya kifedha kwa kutumia mfumo huu.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
