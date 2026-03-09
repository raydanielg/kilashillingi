@extends('layouts.user')

@section('page_title', 'Ripoti')
@section('page_subtitle', 'Hakiki ripoti zako rasmi')

@section('content')
<div class="space-y-6">
    <!-- Selection Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 no-print">
        <button onclick="loadReport('today')" class="p-4 bg-white border border-gray-200 hover:border-emerald-600 rounded-xl shadow-sm transition group text-center">
            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-lg mx-auto mb-2 group-hover:scale-110 transition">
                <i class="fas fa-calendar-day"></i>
            </div>
            <h3 class="text-xs font-bold text-gray-900">Leo</h3>
        </button>

        <button onclick="loadReport('week')" class="p-4 bg-white border border-gray-200 hover:border-orange-600 rounded-xl shadow-sm transition group text-center">
            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center text-lg mx-auto mb-2 group-hover:scale-110 transition">
                <i class="fas fa-calendar-week"></i>
            </div>
            <h3 class="text-xs font-bold text-gray-900">Wiki</h3>
        </button>

        <button onclick="loadReport('month')" class="p-4 bg-white border border-gray-200 hover:border-blue-600 rounded-xl shadow-sm transition group text-center">
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-lg mx-auto mb-2 group-hover:scale-110 transition">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="text-xs font-bold text-gray-900">Mwezi</h3>
        </button>

        <div class="p-4 bg-white border border-gray-100 rounded-xl shadow-sm opacity-50 cursor-not-allowed text-center">
            <div class="w-10 h-10 bg-gray-50 text-gray-400 rounded-lg flex items-center justify-center text-lg mx-auto mb-2">
                <i class="fas fa-print"></i>
            </div>
            <h3 class="text-xs font-bold text-gray-400">Custom</h3>
        </div>
    </div>

    <!-- Preview Container -->
    <div id="report-preview-container" class="min-h-[300px] flex items-center justify-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
        <div class="text-center p-8">
            <div class="text-3xl text-gray-300 mb-3">
                <i class="fas fa-file-invoice"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Hakiki Ripoti Hapa</h3>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .main-content { padding: 0 !important; margin: 0 !important; }
        #report-preview-container { border: none !important; background: white !important; display: block !important; }
    }
</style>

<script>
    async function loadReport(type) {
        const container = document.getElementById('report-preview-container');
        
        container.innerHTML = `
            <div class="text-center p-12">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-emerald-600 border-t-transparent mb-3"></div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Inapakia...</p>
            </div>
        `;

        try {
            const response = await fetch(`{{ route('reports.generate') }}?type=${type}`);
            if (!response.ok) throw new Error('Failed to load report');
            
            const html = await response.text();
            container.innerHTML = html;
            container.classList.remove('bg-gray-50', 'border-dashed');
            
            container.scrollIntoView({ behavior: 'smooth' });
        } catch (error) {
            container.innerHTML = `
                <div class="text-center p-12 text-red-600">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <h3 class="text-xs font-bold uppercase">Hitilafu imetokea</h3>
                </div>
            `;
        }
    }
</script>
@endsection
