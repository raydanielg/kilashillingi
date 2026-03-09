@extends('user.settings.layout')

@section('settings_content')
<section id="security" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden animate-in fade-in duration-500">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
        <i class="fas fa-shield-alt text-emerald-600"></i>
        <h3 class="text-sm font-bold text-gray-900 uppercase">Usalama wa Akaunti</h3>
    </div>
    <div class="p-6 space-y-6">
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Nywila (Password)</h4>
                        <p class="text-xs text-gray-500">Badili nywila yako ili kuimarisha usalama</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="text-emerald-600 hover:text-emerald-700 font-bold text-xs uppercase">Badili</a>
            </div>

            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Uhakiki wa Hatua Mbili</h4>
                        <p class="text-xs text-gray-500">Ongeza ulinzi zaidi kwenye akaunti yako</p>
                    </div>
                </div>
                <span class="px-2 py-1 rounded-md bg-gray-100 text-gray-500 text-[8px] font-bold uppercase tracking-widest">Inakuja</span>
            </div>

            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Vifaa Vilivyounganishwa</h4>
                        <p class="text-xs text-gray-500">Tazama vifaa vyote vilivyoingia kwenye akaunti hii</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
            </div>
        </div>

        <div class="p-4 bg-red-50 rounded-2xl border border-red-100">
            <div class="flex gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-1"></i>
                <div>
                    <h4 class="text-xs font-bold text-red-900 uppercase mb-1">Tahadhari</h4>
                    <p class="text-[11px] text-red-800 leading-relaxed">
                        Kamwe usitoe nywila yako kwa mtu yeyote. Mfumo wa KilaShillingi hautauliza nywila yako kupitia barua pepe au simu.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
