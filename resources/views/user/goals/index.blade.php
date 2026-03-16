@extends('layouts.user')

@section('page_title', 'Malengo')
@section('page_subtitle', 'Tengeneza malengo, fuatilia progress na maliza ndani ya muda')

@section('content')
@php
    $currency = Auth::user()->currency ?? 'TSh';
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-2xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs font-extrabold text-indigo-700 uppercase tracking-widest">Muhtasari</div>
                        <div class="mt-2 text-2xl font-extrabold text-indigo-900">Malengo yako</div>
                        <div class="mt-1 text-sm text-indigo-700">Fuatilia hatua kwa hatua hadi ufikie lengo.</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="px-3 py-2 rounded-xl bg-white/60 border border-indigo-200 text-indigo-800 text-sm font-extrabold">
                            {{ number_format(($goals ?? collect())->where('status', 'active')->count()) }} Active
                        </div>
                        <div class="px-3 py-2 rounded-xl bg-white/60 border border-indigo-200 text-indigo-800 text-sm font-extrabold">
                            {{ number_format(($goals ?? collect())->where('status', 'completed')->count()) }} Done
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse(($goals ?? collect()) as $goal)
                    @php
                        $target = (float) ($goal->target_amount ?? 0);
                        $current = (float) ($goal->current_amount ?? 0);
                        $pct = $target > 0 ? min(100, ($current / $target) * 100) : 0;
                        $isDone = ($goal->status ?? 'active') === 'completed' || ($target > 0 && $current >= $target);
                        $isPaused = ($goal->status ?? 'active') === 'paused';
                        $due = $goal->due_date;
                        $isOverdue = $due && !$isDone && $due->isPast();
                    @endphp

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="text-lg font-extrabold text-gray-900 truncate">{{ $goal->title }}</div>
                                    @if($isDone)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-extrabold uppercase">
                                            <i class="fas fa-check-circle text-[10px]"></i> Imekamilika
                                        </span>
                                    @elseif($isPaused)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase">
                                            <i class="fas fa-pause-circle text-[10px]"></i> Imewekwa pause
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-extrabold uppercase">
                                            <i class="fas fa-clock text-[10px]"></i> Limechelewa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-extrabold uppercase">
                                            <i class="fas fa-bullseye text-[10px]"></i> Inakwenda
                                        </span>
                                    @endif
                                </div>

                                @if($goal->description)
                                    <div class="mt-1 text-sm text-gray-600 leading-relaxed">
                                        {{ $goal->description }}
                                    </div>
                                @endif

                                <div class="mt-4">
                                    <div class="flex items-center justify-between text-xs font-extrabold text-gray-500 uppercase tracking-widest">
                                        <div>Progress</div>
                                        <div>{{ number_format($pct, 0) }}%</div>
                                    </div>
                                    <div class="mt-2 h-3 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $isDone ? 'bg-emerald-600' : ($isOverdue ? 'bg-red-600' : 'bg-indigo-600') }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-700 font-bold">
                                        {{ $currency }} {{ number_format($current, 2) }} <span class="text-gray-400">/</span> {{ $currency }} {{ number_format($target, 2) }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        @if($goal->start_date)
                                            Anza: {{ optional($goal->start_date)->format('d/m/Y') }}
                                            <span class="mx-2">•</span>
                                        @endif
                                        Mwisho: {{ optional($goal->due_date)->format('d/m/Y') ?: '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0 w-full sm:w-auto">
                                <div class="grid grid-cols-1 gap-2">
                                    <button type="button" onclick="openProgressModal({{ $goal->id }}, {{ (float) $goal->current_amount }}, {{ (float) $goal->target_amount }}, '{{ addslashes($goal->title) }}')" class="w-full sm:w-44 px-4 py-2.5 rounded-xl bg-emerald-700 text-white font-extrabold hover:bg-emerald-800 transition">
                                        Ongeza Progress
                                    </button>

                                    <button type="button" onclick="openEditModal({{ $goal->id }}, '{{ addslashes($goal->title) }}', '{{ addslashes($goal->description ?? '') }}', {{ (float) $goal->target_amount }}, '{{ optional($goal->start_date)->format('Y-m-d') }}', '{{ optional($goal->due_date)->format('Y-m-d') }}', '{{ $goal->status }}')" class="w-full sm:w-44 px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 font-extrabold text-gray-900 hover:bg-gray-100 transition">
                                        Hariri
                                    </button>

                                    <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Una uhakika unataka kufuta lengo hili?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full sm:w-44 px-4 py-2.5 rounded-xl border border-red-200 bg-red-50 font-extrabold text-red-700 hover:bg-red-100 transition">
                                            Futa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center text-gray-500 italic">
                        Bado hujatengeneza lengo. Tengeneza lengo la kwanza upande wa kulia.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm sticky top-24">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-extrabold text-gray-900 uppercase">Tengeneza Lengo</div>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700">
                        <i class="fas fa-plus"></i>
                    </div>
                </div>

                <form action="{{ route('goals.store') }}" method="POST" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Jina la Lengo</label>
                        <input name="title" required maxlength="255" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition" placeholder="mfano: Nunua Laptop">
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Maelezo (hiari)</label>
                        <textarea name="description" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition" placeholder="mfano: Akiba ya miezi 3"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Target ({{ $currency }})</label>
                            <input name="target_amount" type="number" min="0" step="0.01" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Umefika ({{ $currency }})</label>
                            <input name="current_amount" type="number" min="0" step="0.01" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition" placeholder="0.00">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Anza (hiari)</label>
                            <input name="start_date" type="date" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Mwisho (hiari)</label>
                            <input name="due_date" type="date" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition">
                        </div>
                    </div>

                    <button type="submit" class="w-full h-12 rounded-xl bg-indigo-700 text-white font-extrabold hover:bg-indigo-800 transition">
                        Hifadhi Lengo
                    </button>

                    <div class="text-xs text-gray-500 leading-relaxed">
                        Tip: Unaweza kuongeza progress taratibu kadri unavyoweka akiba.
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div id="progress-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeProgressModal()"></div>
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-start justify-between">
                <div>
                    <div class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">Ongeza Progress</div>
                    <div id="progress-title" class="mt-1 text-lg font-extrabold text-gray-900">-</div>
                </div>
                <button type="button" onclick="closeProgressModal()" class="p-2 rounded-xl hover:bg-gray-50">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>
            <form id="progress-form" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Umefika ({{ $currency }})</label>
                    <input id="progress-current" name="current_amount" type="number" min="0" step="0.01" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-600 focus:border-transparent outline-none transition" placeholder="0.00">
                    <div id="progress-hint" class="mt-2 text-xs text-gray-500">-</div>
                </div>
                <button type="submit" class="w-full h-12 rounded-xl bg-emerald-700 text-white font-extrabold hover:bg-emerald-800 transition">
                    Hifadhi Progress
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeEditModal()"></div>
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-start justify-between">
                <div>
                    <div class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">Hariri Lengo</div>
                    <div class="mt-1 text-lg font-extrabold text-gray-900">Badilisha taarifa</div>
                </div>
                <button type="button" onclick="closeEditModal()" class="p-2 rounded-xl hover:bg-gray-50">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>
            <form id="edit-form" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Jina la Lengo</label>
                    <input id="edit-title" name="title" required maxlength="255" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition" placeholder="mfano: Nunua Laptop">
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Maelezo (hiari)</label>
                    <textarea id="edit-description" name="description" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Target ({{ $currency }})</label>
                    <input id="edit-target" name="target_amount" type="number" min="0" step="0.01" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Anza</label>
                        <input id="edit-start" name="start_date" type="date" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Mwisho</label>
                        <input id="edit-due" name="due_date" type="date" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase tracking-widest mb-2">Hali</label>
                    <select id="edit-status" name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-600 focus:border-transparent outline-none transition">
                        <option value="active">Active</option>
                        <option value="paused">Paused</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <button type="submit" class="w-full h-12 rounded-xl bg-indigo-700 text-white font-extrabold hover:bg-indigo-800 transition">
                    Hifadhi Mabadiliko
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openProgressModal(id, current, target, title) {
        const modal = document.getElementById('progress-modal');
        const form = document.getElementById('progress-form');
        const input = document.getElementById('progress-current');
        const hint = document.getElementById('progress-hint');
        const titleEl = document.getElementById('progress-title');

        titleEl.textContent = title || 'Lengo';
        form.action = '{{ url('/goals') }}/' + id + '/progress';
        input.value = (current ?? 0);
        hint.textContent = 'Target: {{ $currency }} ' + Number(target ?? 0).toLocaleString();

        modal.classList.remove('hidden');
    }

    function closeProgressModal() {
        document.getElementById('progress-modal').classList.add('hidden');
    }

    function openEditModal(id, title, description, target, startDate, dueDate, status) {
        const modal = document.getElementById('edit-modal');
        const form = document.getElementById('edit-form');

        form.action = '{{ url('/goals') }}/' + id;

        document.getElementById('edit-title').value = title || '';
        document.getElementById('edit-description').value = description || '';
        document.getElementById('edit-target').value = (target ?? 0);
        document.getElementById('edit-start').value = startDate || '';
        document.getElementById('edit-due').value = dueDate || '';
        document.getElementById('edit-status').value = status || 'active';

        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }
</script>
@endsection
