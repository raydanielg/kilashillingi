@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Dashboard ya Admin — KilaShillingi</h1>
        <div class="text-muted text-sm font-weight-bold uppercase">
            {{ now('Africa/Dar_es_Salaam')->format('l, d F Y') }}
        </div>
    </div>
@stop

@section('content')
    <!-- 1. Quick Stats (Karatabs) -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info elevation-2">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Watumiaji Wote</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">Orodha ya Watumiaji <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success elevation-2">
                <div class="inner">
                    <h3>{{ $totalTransactions }}</h3>
                    <p>Miamala Yote</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <a href="#" class="small-box-footer">Chambua Takwimu <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning elevation-2">
                <div class="inner">
                    <h3>{{ $recentUsers->count() }}</h3>
                    <p>Wapya Leo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="#" class="small-box-footer">Ripoti ya Watumiaji <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger elevation-2">
                <div class="inner">
                    <h3 style="font-size: 1.6rem">TSh {{ number_format($totalVolume, 0) }}</h3>
                    <p>Mzunguko wa Fedha</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="#" class="small-box-footer">Mchanganuo Kamili <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- 2. Charts and Recent Activity -->
    <div class="row">
        <!-- Main Chart Area (Statistics Placeholder) -->
        <div class="col-lg-8">
            <div class="card card-outline card-primary elevation-1">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold uppercase">
                        <i class="fas fa-chart-line mr-1"></i> Takwimu za Mfumo (Mwezi Huu)
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="system-stats-chart" height="300"></canvas>
                    </div>
                    <div class="d-flex flex-row justify-content-end gap-4 text-sm font-weight-bold">
                        <span class="mr-3 text-emerald-600">
                            <i class="fas fa-square mr-1"></i> Mapato
                        </span>
                        <span class="text-red-600">
                            <i class="fas fa-square mr-1"></i> Matumizi
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users Side Table -->
        <div class="col-lg-4">
            <div class="card card-outline card-info elevation-1">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold uppercase">
                        <i class="fas fa-user-plus mr-1"></i> Watumiaji Wapya
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="users-list clearfix">
                        @foreach($recentUsers as $user)
                        <li>
                            <div class="w-12 h-12 bg-gray-200 rounded-circle flex items-center justify-center mx-auto mb-1 font-weight-bold text-gray-600">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <a class="users-list-name text-xs font-weight-bold" href="#">{{ explode(' ', $user->name)[0] }}</a>
                            <span class="users-list-date text-[10px]">{{ $user->created_at->diffForHumans() }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="javascript:void(0)" class="uppercase font-weight-bold text-xs">Tazama Watumiaji Wote</a>
                </div>
            </div>

            <!-- Premium Box Placeholder -->
            <div class="info-box bg-gradient-navy elevation-2">
                <span class="info-box-icon"><i class="fas fa-star text-warning"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text font-weight-bold uppercase text-xs">Mapato ya Premium</span>
                    <span class="info-box-number text-lg">TSh 0.00</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description text-[10px]">Lengo: TSh 500,000 mwezi huu</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .users-list>li { width: 33.3%; }
        .bg-gradient-navy { background: #001f3f radial-gradient(circle, #001f3f 0%, #001433 100%); color: white; }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function () {
            const ctx = document.getElementById('system-stats-chart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Mapato',
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.1)',
                        data: [65, 59, 80, 81, 56, 55],
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Matumizi',
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.1)',
                        data: [28, 48, 40, 19, 86, 27],
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
@stop
