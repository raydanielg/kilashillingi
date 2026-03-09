@extends('user.settings.layout')

@section('settings_content')
<section id="preferences" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden animate-in fade-in duration-500">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
        <i class="fas fa-sliders-h text-emerald-600"></i>
        <h3 class="text-sm font-bold text-gray-900 uppercase">Mapendeleo ya Mfumo</h3>
    </div>
    <div class="p-6 space-y-6">
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center">
                        <i class="fas fa-moon"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Modi ya Giza (Dark Mode)</h4>
                        <p class="text-xs text-gray-500">Badili muonekano wa mfumo kuwa mweusi</p>
                    </div>
                </div>
                <div class="w-10 h-5 bg-gray-200 rounded-full relative cursor-pointer">
                    <div class="absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition-all"></div>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-language"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Lugha ya Mfumo</h4>
                        <p class="text-xs text-gray-500">Chagua lugha unayopendelea kutumia</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-emerald-700">Kiswahili</span>
            </div>

            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Taarifa (Notifications)</h4>
                        <p class="text-xs text-gray-500">Dhibiti namna unavyopata taarifa za mfumo</p>
                    </div>
                </div>
                <div class="w-10 h-5 bg-emerald-600 rounded-full relative cursor-pointer">
                    <div class="absolute right-1 top-1 bg-white w-3 h-3 rounded-full transition-all"></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
