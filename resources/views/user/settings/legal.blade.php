@extends('user.settings.layout')

@section('settings_content')
<section id="legal" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden animate-in fade-in duration-500">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
        <i class="fas fa-gavel text-emerald-600"></i>
        <h3 class="text-sm font-bold text-gray-900 uppercase">Sheria na Masharti</h3>
    </div>
    <div class="p-6 space-y-8">
        <div class="space-y-4">
            <h4 class="text-md font-bold text-gray-900 border-l-4 border-emerald-600 pl-3">1. Sera ya Faragha (Privacy Policy)</h4>
            <p class="text-sm text-gray-600 leading-relaxed">
                Mfumo wa KilaShillingi unathamini faragha yako. Taarifa zote unazoingiza, zikiwemo mapato, matumizi, na madeni, ni siri yako binafsi. Hatushiriki data hizi na upande wowote wa tatu bila idhini yako.
            </p>
        </div>

        <div class="space-y-4">
            <h4 class="text-md font-bold text-gray-900 border-l-4 border-emerald-600 pl-3">2. Masharti ya Matumizi</h4>
            <p class="text-sm text-gray-600 leading-relaxed">
                Kwa kutumia mfumo huu, unakubali kuingiza taarifa za kweli kwa ajili ya usimamizi wako wa fedha. Mfumo huu ni chombo cha ushauri tu; maamuzi ya mwisho ya kifedha yanabaki kuwa jukumu la mtumiaji.
            </p>
        </div>

        <div class="space-y-4">
            <h4 class="text-md font-bold text-gray-900 border-l-4 border-emerald-600 pl-3">3. Usalama wa Akaunti</h4>
            <p class="text-sm text-gray-600 leading-relaxed">
                Ni wajibu wako kutunza siri ya nywila (password) yako. Ukiona muamala au mabadiliko usiyoyatambua, tafadhali badili nywila yako mara moja kupitia sehemu ya Usalama.
            </p>
        </div>

        <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
            <div class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Toleo la App: 1.0.0 (Beta)</div>
            <div class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">KilaShillingi © {{ date('Y') }}</div>
        </div>
    </div>
</section>
@endsection
