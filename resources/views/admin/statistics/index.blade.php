@extends('adminlte::page')

@section('title', 'Admin - Takwimu')

@section('content_header')
    <h1>Takwimu za Mfumo</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-user-plus"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text uppercase font-weight-bold">Waliojiunga Wiki Hii</span>
                    <span class="info-box-number text-lg">{{ $weeklyRegistrations }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text uppercase font-weight-bold">Watumiaji Wote</span>
                    <span class="info-box-number text-lg">{{ $totalUsers }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text uppercase font-weight-bold">Miamala ya Mfumo</span>
                    <span class="info-box-number text-lg">{{ $totalTransactions }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold uppercase">Mwenendo wa Usajili (Siku 7 zilizopita)</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="regChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('regChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Usajili Mpya',
                data: {!! json_encode($chartData) !!},
                backgroundColor: '#17a2b8',
                borderColor: '#17a2b8',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
@stop
