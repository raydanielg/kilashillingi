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
                    <div class="mt-2 text-3xl font-extrabold {{ ($balance ?? 0) >= 0 ? 'text-emerald-800' : 'text-red-700' }}">{{ Auth::user()->currency ?? 'TSh' }} {{ number_format($balance ?? 0, 2) }}</div>
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
                    <div class="mt-2 text-2xl font-extrabold text-emerald-800">{{ Auth::user()->currency ?? 'TSh' }} {{ number_format($monthIncome ?? 0, 2) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-red-50 border border-red-100">
                    <div class="text-xs font-extrabold text-red-800 uppercase tracking-widest">Matumizi ya mwezi huu</div>
                    <div class="mt-2 text-2xl font-extrabold text-red-700">{{ Auth::user()->currency ?? 'TSh' }} {{ number_format($monthExpense ?? 0, 2) }}</div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200">
                    <div class="text-xs font-extrabold text-emerald-900 uppercase tracking-widest">Mapato ya leo</div>
                    <div class="mt-2 text-2xl font-extrabold text-emerald-800">{{ Auth::user()->currency ?? 'TSh' }} {{ number_format($todayIncome ?? 0, 2) }}</div>
                    <div class="mt-1 text-xs text-emerald-600">Miamala leo: {{ number_format($todayTransactionsCount ?? 0) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200">
                    <div class="text-xs font-extrabold text-blue-900 uppercase tracking-widest">Miamala (mwezi huu)</div>
                    <div class="mt-2 text-2xl font-extrabold text-blue-800">{{ number_format($monthTransactionsCount ?? 0) }}</div>
                    <div class="mt-1 text-xs text-blue-600">Jumla ya miamala ya mwezi huu</div>
                </div>
            </div>

            <div class="mt-4 p-5 rounded-2xl bg-gradient-to-br from-red-50 to-red-100 border border-red-200">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs font-extrabold text-red-900 uppercase tracking-widest">Matumizi ya leo</div>
                        <div class="mt-2 text-2xl font-extrabold text-red-800">{{ Auth::user()->currency ?? 'TSh' }} {{ number_format($todayExpense ?? 0, 2) }}</div>
                        <div class="mt-1 text-xs text-red-600">Jana: {{ Auth::user()->currency ?? 'TSh' }} {{ number_format($yesterdayExpense ?? 0, 2) }}</div>
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

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-2xl p-6">
            <div class="text-xs font-extrabold text-purple-900 uppercase tracking-widest">Ongeza Haraka</div>
            <div class="mt-4 grid grid-cols-1 gap-3">
                <a href="{{ route('expenses.create') }}" class="px-4 py-3 rounded-xl border border-red-200 bg-red-50 font-extrabold text-red-700 hover:bg-red-100 transition-colors">Matumizi</a>
                <a href="{{ route('income.create') }}" class="px-4 py-3 rounded-xl border border-emerald-200 bg-emerald-50 font-extrabold text-emerald-700 hover:bg-emerald-100 transition-colors">Mapato</a>
                <a href="{{ route('history.index') }}" class="px-4 py-3 rounded-xl border border-blue-200 bg-blue-50 font-extrabold text-blue-700 hover:bg-blue-100 transition-colors">Historia</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-extrabold text-indigo-900">Mwenendo wa matumizi (kila siku)</div>
                    <div class="text-sm text-indigo-600">Siku 7 zilizopita</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="rounded-2xl bg-gradient-to-br from-indigo-50/70 to-white/40 backdrop-blur border border-indigo-200/70 p-3">
                    <canvas id="daily-expense-line" height="110"></canvas>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-extrabold text-indigo-900">Mapato vs Matumizi</div>
                        <div class="text-sm text-indigo-600">Mlinganisho wa siku 7</div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div class="rounded-2xl bg-gradient-to-br from-emerald-50/60 to-white/35 backdrop-blur border border-indigo-200/70 p-3">
                    <canvas id="income-expense-bar" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-extrabold text-orange-900">Mgawanyo wa matumizi</div>
                    <div class="text-sm text-orange-600">Kwa makundi (mwezi huu)</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="rounded-2xl bg-gradient-to-br from-orange-50/70 to-white/40 backdrop-blur border border-orange-200/70 p-3">
                    <canvas id="expense-pie" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-teal-50 to-teal-100 border border-teal-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-teal-200 flex items-center justify-between">
                <h2 class="font-extrabold text-teal-900">Miamala za Hivi Karibuni</h2>
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
                                {{ Auth::user()->currency ?? 'TSh' }} {{ number_format($transaction->amount, 2) }}
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

        <div class="bg-gradient-to-br from-pink-50 to-pink-100 border border-pink-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-pink-200 flex items-center justify-between">
                <h2 class="font-extrabold text-pink-900">Madeni Yasiyolipwa</h2>
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
                            <div class="font-extrabold text-gray-900">{{ Auth::user()->currency ?? 'TSh' }} {{ number_format($debt->amount, 2) }}</div>
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

        const ui = {
            text: '#334155',
            muted: '#64748b',
            grid: 'rgba(15, 23, 42, 0.08)',
            tooltipBg: 'rgba(15, 23, 42, 0.92)',
        };

        Chart.defaults.color = ui.text;
        Chart.defaults.font.family = "Plus Jakarta Sans, ui-sans-serif, system-ui";
        Chart.defaults.font.weight = '700';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.legend.labels.pointStyle = 'rectRounded';

        const chartAreaBg = {
            id: 'chartAreaBg',
            beforeDraw(chart, args, opts) {
                const { ctx, chartArea } = chart;
                if (!chartArea) return;
                const { left, top, right, bottom } = chartArea;
                ctx.save();
                ctx.fillStyle = opts?.color || 'rgba(255, 255, 255, 0.25)';
                ctx.fillRect(left, top, right - left, bottom - top);
                ctx.restore();
            }
        };

        const lineLabels = @json($dailyExpenseLabels ?? []);
        const lineData = @json($dailyExpenseData ?? []);
        const incomeData = @json($dailyIncomeData ?? []);
        const pieLabels = @json($pieLabels ?? []);
        const pieData = @json($pieData ?? []);

        new Chart(lineEl, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [
                    {
                        label: 'Matumizi (' + ({{ json_encode(Auth::user()->currency ?? 'TSh') }}) + ')',
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
                    chartAreaBg: { color: 'rgba(99, 102, 241, 0.10)' },
                    tooltip: {
                        backgroundColor: ui.tooltipBg,
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255,255,255,0.15)',
                        borderWidth: 1,
                        callbacks: {
                            label: function (ctx) {
                                const v = Number(ctx.parsed.y || 0);
                                return ({{ json_encode(Auth::user()->currency ?? 'TSh') }}) + ' ' + v.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: ui.muted, font: { weight: '800' } },
                    },
                    y: {
                        grid: { color: ui.grid },
                        ticks: {
                            color: ui.muted,
                            callback: (v) => ({{ json_encode(Auth::user()->currency ?? 'TSh') }}) + ' ' + Number(v).toLocaleString(),
                        },
                    }
                }
            },
            plugins: [chartAreaBg],
        });

        const barEl = document.getElementById('income-expense-bar');
        if (barEl) {
            new Chart(barEl, {
                type: 'bar',
                data: {
                    labels: lineLabels,
                    datasets: [
                        {
                            label: 'Mapato',
                            data: incomeData,
                            backgroundColor: 'rgba(5, 150, 105, 0.25)',
                            borderColor: '#059669',
                            borderWidth: 2,
                            borderRadius: 10,
                        },
                        {
                            label: 'Matumizi',
                            data: lineData,
                            backgroundColor: 'rgba(220, 38, 38, 0.25)',
                            borderColor: '#dc2626',
                            borderWidth: 2,
                            borderRadius: 10,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: ui.text,
                                font: { weight: '800' },
                            },
                        },
                        chartAreaBg: { color: 'rgba(16, 185, 129, 0.08)' },
                        tooltip: {
                            backgroundColor: ui.tooltipBg,
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: 'rgba(255,255,255,0.15)',
                            borderWidth: 1,
                            callbacks: {
                                label: function (ctx) {
                                    const v = Number(ctx.parsed.y || 0);
                                    return ctx.dataset.label + ': ' + ({{ json_encode(Auth::user()->currency ?? 'TSh') }}) + ' ' + v.toLocaleString();
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: ui.muted, font: { weight: '800' } },
                        },
                        y: {
                            grid: { color: ui.grid },
                            ticks: {
                                color: ui.muted,
                                callback: (v) => ({{ json_encode(Auth::user()->currency ?? 'TSh') }}) + ' ' + Number(v).toLocaleString(),
                            },
                        },
                    },
                },
                plugins: [chartAreaBg],
            });
        }

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
                            color: ui.text,
                            font: { weight: '800' },
                        }
                    },
                    chartAreaBg: { color: 'rgba(249, 115, 22, 0.10)' },
                    tooltip: {
                        backgroundColor: ui.tooltipBg,
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255,255,255,0.15)',
                        borderWidth: 1,
                        callbacks: {
                            label: function (ctx) {
                                const v = Number(ctx.parsed || 0);
                                return ctx.label + ': ' + ({{ json_encode(Auth::user()->currency ?? 'TSh') }}) + ' ' + v.toLocaleString();
                            }
                        }
                    }
                }
            },
            plugins: [chartAreaBg],
        });
    })();
</script>
@endsection
