@extends('layouts.user')

@section('page_title', 'Dashibodi')
@section('page_subtitle', 'Muhtasari wa mapato, matumizi na madeni')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-emerald-700 to-red-600 rounded-3xl p-6 sm:p-8 text-white overflow-hidden relative">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-12 -left-12 w-56 h-56 bg-white/10 rounded-full"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div>
                <div class="text-white/80 text-xs font-extrabold uppercase tracking-widest">Karibu Nyumbani</div>
                <div class="mt-2 text-3xl sm:text-4xl font-extrabold leading-tight">
                    Hello 👋, {{ Auth::user()->name }}
                </div>
                <div class="mt-2 text-white/80 text-sm max-w-xl">
                    Muhtasari wa hali ya pesa zako kwa sasa. Rekodi kila shillingi na uone maendeleo yako kwa urahisi.
                </div>
            </div>

            <div class="bg-white/10 border border-white/20 rounded-2xl px-5 py-4 min-w-[240px]">
                <div class="text-xs font-extrabold uppercase tracking-widest text-white/80">Muda wa sasa</div>
                <div id="live-clock" class="mt-2 text-2xl font-extrabold">--:--:--</div>
                <div id="live-date" class="mt-1 text-sm text-white/80">--</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">Salio la sasa</div>
                    <div class="mt-2 text-3xl font-extrabold {{ ($balance ?? 0) >= 0 ? 'text-emerald-800' : 'text-red-700' }}">TSh {{ number_format($balance ?? 0, 2) }}</div>
                    <div class="mt-2 text-sm text-gray-500">Muhtasari wa mwezi huu</div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('expenses.create') }}" class="px-4 py-3 rounded-xl bg-red-600 text-white font-extrabold">Ongeza Matumizi</a>
                    <a href="{{ route('income.create') }}" class="px-4 py-3 rounded-xl bg-emerald-700 text-white font-extrabold">Ongeza Mapato</a>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-100">
                    <div class="text-xs font-extrabold text-emerald-900 uppercase tracking-widest">Mapato ya mwezi huu</div>
                    <div class="mt-2 text-2xl font-extrabold text-emerald-800">TSh {{ number_format($monthIncome ?? 0, 2) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-red-50 border border-red-100">
                    <div class="text-xs font-extrabold text-red-800 uppercase tracking-widest">Matumizi ya mwezi huu</div>
                    <div class="mt-2 text-2xl font-extrabold text-red-700">TSh {{ number_format($monthExpense ?? 0, 2) }}</div>
                </div>
            </div>

            <div class="mt-4 p-5 rounded-2xl bg-white border border-gray-200">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">Matumizi ya leo</div>
                        <div class="mt-2 text-2xl font-extrabold text-gray-900">TSh {{ number_format($todayExpense ?? 0, 2) }}</div>
                        <div class="mt-1 text-xs text-gray-500">Jana: TSh {{ number_format($yesterdayExpense ?? 0, 2) }}</div>
                    </div>

                    @php
                        $trendUp = ($expenseTrendDirection ?? 'up') === 'up';
                        $trendPercent = (float) ($expenseTrendPercent ?? 0);
                    @endphp
                    <div class="text-right">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl {{ $trendUp ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-800' }}">
                            <span class="text-lg leading-none font-extrabold">{{ $trendUp ? '↑' : '↓' }}</span>
                            <span class="text-sm font-extrabold">{{ number_format(abs($trendPercent), 1) }}%</span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">{{ $trendUp ? 'Yameongezeka' : 'Yamepungua' }} ukilinganisha na jana</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">Ongeza Haraka</div>
            <div class="mt-4 grid grid-cols-1 gap-3">
                <a href="{{ route('expenses.create') }}" class="px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 font-extrabold text-gray-900">Matumizi</a>
                <a href="{{ route('income.create') }}" class="px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 font-extrabold text-gray-900">Mapato</a>
                <a href="{{ route('history.index') }}" class="px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 font-extrabold text-gray-900">Historia</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-extrabold text-gray-900">Mwenendo wa matumizi (kila siku)</div>
                    <div class="text-sm text-gray-500">Siku 14 zilizopita</div>
                </div>
            </div>

            <div class="mt-6">
                <canvas id="daily-expense-line" height="110"></canvas>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-extrabold text-gray-900">Mgawanyo wa matumizi</div>
                    <div class="text-sm text-gray-500">Kwa makundi (mwezi huu)</div>
                </div>
            </div>

            <div class="mt-6">
                <canvas id="expense-pie" height="220"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-extrabold text-gray-900">Miamala za Hivi Karibuni</h2>
                <a href="{{ route('transactions.index') }}" class="text-sm font-extrabold text-emerald-700 hover:underline">Tazama zote</a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse(($transactions ?? collect()) as $transaction)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-gray-900">{{ $transaction->description ?: '-' }}</div>
                            <div class="text-xs text-gray-500">{{ optional($transaction->date)->format('d/m/Y') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-extrabold {{ $transaction->type === 'income' ? 'text-emerald-700' : 'text-red-600' }}">
                                TSh {{ number_format($transaction->amount, 2) }}
                            </div>
                            <div class="text-xs font-bold uppercase tracking-widest text-gray-400">
                                {{ $transaction->type === 'income' ? 'Mapato' : 'Matumizi' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-gray-500 italic">Hakuna miamala kwa sasa.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-extrabold text-gray-900">Madeni Yasiyolipwa</h2>
                <a href="{{ route('debts.index') }}" class="text-sm font-extrabold text-emerald-700 hover:underline">Tazama yote</a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse(($debts ?? collect()) as $debt)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-gray-900">{{ $debt->person_name }}</div>
                            <div class="text-xs text-gray-500">{{ $debt->type === 'lent' ? 'Nimeopesha' : 'Nimekopeshwa' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-extrabold text-gray-900">TSh {{ number_format($debt->amount, 2) }}</div>
                            <div class="text-xs text-gray-500">Rudisha: {{ optional($debt->due_date)->format('d/m/Y') ?: '-' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-gray-500 italic">Hakuna deni linalosubiri.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const clockEl = document.getElementById('live-clock');
        const dateEl = document.getElementById('live-date');
        if (!clockEl || !dateEl) return;

        const fmtTime = new Intl.DateTimeFormat('sw-TZ', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Africa/Dar_es_Salaam',
        });

        const fmtDate = new Intl.DateTimeFormat('sw-TZ', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: '2-digit',
            timeZone: 'Africa/Dar_es_Salaam',
        });

        const tick = () => {
            const now = new Date();
            clockEl.textContent = fmtTime.format(now);
            dateEl.textContent = fmtDate.format(now);
        };

        tick();
        setInterval(tick, 1000);
    })();
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const lineEl = document.getElementById('daily-expense-line');
        const pieEl = document.getElementById('expense-pie');
        if (!lineEl || !pieEl || !window.Chart) return;

        const lineLabels = @json($dailyExpenseLabels ?? []);
        const lineData = @json($dailyExpenseData ?? []);
        const pieLabels = @json($pieLabels ?? []);
        const pieData = @json($pieData ?? []);

        new Chart(lineEl, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [
                    {
                        label: 'Matumizi (TSh)',
                        data: lineData,
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.10)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const v = Number(ctx.parsed.y || 0);
                                return 'TSh ' + v.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: { weight: '700' } },
                    },
                    y: {
                        grid: { color: 'rgba(107, 114, 128, 0.12)' },
                        ticks: {
                            color: '#6b7280',
                            callback: (v) => 'TSh ' + Number(v).toLocaleString(),
                        },
                    }
                }
            }
        });

        const pieColors = [
            '#059669',
            '#dc2626',
            '#0ea5e9',
            '#f59e0b',
            '#8b5cf6',
            '#10b981',
            '#ef4444',
            '#64748b',
            '#22c55e',
        ];

        new Chart(pieEl, {
            type: 'pie',
            data: {
                labels: pieLabels,
                datasets: [
                    {
                        data: pieData,
                        backgroundColor: pieLabels.map((_, i) => pieColors[i % pieColors.length]),
                        borderColor: '#ffffff',
                        borderWidth: 2,
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            color: '#111827',
                            font: { weight: '800' },
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const v = Number(ctx.parsed || 0);
                                return ctx.label + ': TSh ' + v.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    })();
</script>
@endsection
