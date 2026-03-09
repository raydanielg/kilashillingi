@extends('adminlte::page')

@section('title', 'Admin - Ripoti')

@section('content_header')
    <h1>Ripoti Kuu za Mfumo</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">RIPOTI YA WATUMIAJI</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-gray-50">
                                <th>Aina ya Mtumiaji</th>
                                <th class="text-right">Idadi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userStats as $stat)
                            <tr>
                                <td class="text-uppercase">{{ $stat->role }}</td>
                                <td class="text-right font-weight-bold">{{ $stat->count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">RIPOTI YA MIAMALA</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-gray-50">
                                <th>Aina</th>
                                <th class="text-right">Idadi</th>
                                <th class="text-right">Jumla (TSh)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactionStats as $stat)
                            <tr>
                                <td class="text-uppercase">{{ $stat->type }}</td>
                                <td class="text-right font-weight-bold">{{ $stat->count }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($stat->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
