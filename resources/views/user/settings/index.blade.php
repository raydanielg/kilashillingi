@extends('user.settings.layout')

@section('settings_content')
<section id="profile" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden animate-in fade-in duration-500">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
        <i class="fas fa-user-circle text-emerald-600"></i>
        <h3 class="text-sm font-bold text-gray-900 uppercase">Taarifa Binafsi</h3>
    </div>
    <div class="p-6 space-y-6">
        <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100">
            <div class="w-24 h-24 rounded-2xl bg-emerald-700 flex items-center justify-center text-white text-3xl font-bold shadow-lg overflow-hidden shrink-0">
                @if(Auth::user()->avatar)
                    <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    {{ substr(Auth::user()->name, 0, 1) }}
                @endif
            </div>
            <div class="text-center sm:text-left space-y-1">
                <h4 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h4>
                <p class="text-sm text-gray-500 lowercase">{{ Auth::user()->email }}</p>
                <div class="pt-2">
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Akaunti ya Mtumiaji</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jina Kamili</label>
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Barua Pepe</label>
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-900">{{ Auth::user()->email }}</div>
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Namba ya Simu</label>
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-900">{{ Auth::user()->phone ?? 'Hujatolewa' }}</div>
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Muda wa Kujiunga</label>
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-900">{{ Auth::user()->created_at->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="pt-4 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('profile.edit') }}" class="flex-1 bg-emerald-700 text-white font-bold py-3 rounded-xl hover:bg-emerald-800 transition flex items-center justify-center gap-2 text-sm shadow-md">
                <i class="fas fa-user-edit text-xs"></i>
                <span>Hariri Profaili</span>
            </a>
            <button class="flex-1 bg-white border border-gray-200 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-camera text-xs"></i>
                <span>Badili Picha</span>
            </button>
        </div>
    </div>
</section>

<!-- Reset Data Section -->
<section id="reset" class="mt-8 bg-white border border-red-100 rounded-2xl shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="px-6 py-4 border-b border-red-50 bg-red-50/30 flex items-center gap-2">
        <i class="fas fa-trash-alt text-red-600"></i>
        <h3 class="text-sm font-bold text-gray-900 uppercase">Anza Upya (Reset Data)</h3>
    </div>
    <div class="p-6">
        <div class="flex items-start gap-4 p-4 bg-red-50 rounded-xl border border-red-100 mb-6">
            <i class="fas fa-exclamation-triangle text-red-600 text-xl mt-1"></i>
            <div class="space-y-1">
                <h4 class="text-sm font-bold text-red-900 uppercase tracking-tight">Tahadhari Muhimu!</h4>
                <p class="text-xs text-red-700 leading-relaxed">
                    Ukibofya kitufe cha "Anza Upya", data zako zote (Miamala, Madeni, Bajeti, na Vikumbusho) zitafutwa kabisa na hazitaweza kurudishwa. Akaunti yako na maelezo ya kuingia (Login) yataendelea kubaki.
                </p>
            </div>
        </div>

        <button onclick="confirmReset()" class="w-full sm:w-auto px-8 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition flex items-center justify-center gap-2 text-sm shadow-lg shadow-red-900/10">
            <i class="fas fa-sync-alt text-xs"></i>
            <span>FUTA DATA NA ANZA UPYA</span>
        </button>
    </div>
</section>

<!-- Reset Confirmation Modal -->
<div id="reset-modal" class="fixed inset-0 bg-black/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl transform transition-all">
        <div class="p-8 text-center space-y-6">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-circle text-red-600 text-4xl"></i>
            </div>
            <div class="space-y-2">
                <h3 class="text-xl font-bold text-gray-900 uppercase">Je, una uhakika?</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Hatua hii hairejesheki. Data zako zote zitafutwa na utaanza upya kurekodi miamala yako.
                </p>
            </div>
            
            <div class="flex flex-col gap-3 pt-4">
                <form action="{{ route('settings.reset-data') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 text-white font-bold py-4 rounded-2xl hover:bg-red-700 transition shadow-lg shadow-red-900/20 uppercase tracking-widest text-xs">
                        NDIO, FUTA DATA ZOTE
                    </button>
                </form>
                <button onclick="closeResetModal()" class="w-full bg-gray-100 text-gray-700 font-bold py-4 rounded-2xl hover:bg-gray-200 transition uppercase tracking-widest text-xs">
                    HAPANA, GHAIRISHA
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmReset() {
        const modal = document.getElementById('reset-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeResetModal() {
        const modal = document.getElementById('reset-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Funga modal kwa kubonyeza nje
    window.onclick = function(event) {
        const modal = document.getElementById('reset-modal');
        if (event.target == modal) {
            closeResetModal();
        }
    }
</script>
@endsection
