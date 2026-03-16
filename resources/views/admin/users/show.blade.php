@extends('adminlte::page')

@section('title', 'Admin - User Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">User Details</h1>
            <div class="text-muted text-sm">Mtumiaji #{{ $user->id }}</div>
        </div>
        <div>
            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Rudi Watumiaji
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <img src="{{ $user->avatar_url ?? $user->adminlte_image() }}" alt="Avatar" class="img-circle elevation-2" style="width: 64px; height: 64px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1">
                            <div class="h5 mb-1 font-weight-bold">{{ $user->name }}</div>
                            <div class="text-sm text-muted">{{ $user->email }}</div>
                            <div class="text-sm text-muted">{{ $user->phone ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="badge {{ $user->role === 'admin' ? 'badge-danger' : 'badge-success' }}">{{ strtoupper($user->role) }}</span>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-6">
                            <div class="text-muted text-xs font-weight-bold uppercase">Currency</div>
                            <div class="font-weight-bold">{{ $user->currency ?? '-' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted text-xs font-weight-bold uppercase">Joined</div>
                            <div class="font-weight-bold">{{ optional($user->created_at)->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold uppercase text-sm">
                        <i class="fas fa-layer-group mr-1"></i> Summary
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-muted text-xs font-weight-bold uppercase">Transactions</div>
                            <div class="h5 mb-0">{{ number_format($transactionsCount) }}</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-muted text-xs font-weight-bold uppercase">Budgets</div>
                            <div class="h5 mb-0">{{ number_format($budgetsCount) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted text-xs font-weight-bold uppercase">Goals</div>
                            <div class="h5 mb-0">{{ number_format($goalsCount) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted text-xs font-weight-bold uppercase">Debts</div>
                            <div class="h5 mb-0">{{ number_format($debtsCount) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-arrow-down"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text uppercase font-weight-bold text-xs">Mapato (All)</span>
                            <span class="info-box-number">{{ number_format($incomeTotal, 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-arrow-up"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text uppercase font-weight-bold text-xs">Matumizi (All)</span>
                            <span class="info-box-number">{{ number_format($expenseTotal, 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-wallet"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text uppercase font-weight-bold text-xs">Balance (All)</span>
                            <span class="info-box-number">{{ number_format($balanceTotal, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text uppercase font-weight-bold text-xs">Mapato (Mwezi)</span>
                            <span class="info-box-number">{{ number_format($incomeMonth, 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-calendar-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text uppercase font-weight-bold text-xs">Matumizi (Mwezi)</span>
                            <span class="info-box-number">{{ number_format($expenseMonth, 0) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-calendar-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text uppercase font-weight-bold text-xs">Balance (Mwezi)</span>
                            <span class="info-box-number">{{ number_format($balanceMonth, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold uppercase text-sm">
                        <i class="fas fa-exchange-alt mr-1"></i> Recent Transactions
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Aina</th>
                                    <th>Maelezo</th>
                                    <th>Kiasi</th>
                                    <th>Tarehe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $tx)
                                    <tr>
                                        <td>{{ $tx->id }}</td>
                                        <td>
                                            <span class="badge {{ $tx->type === 'income' ? 'badge-success' : 'badge-danger' }}">
                                                {{ strtoupper($tx->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $tx->description ?? '-' }}</td>
                                        <td class="font-weight-bold">{{ number_format($tx->amount, 0) }}</td>
                                        <td>{{ optional($tx->date)->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted p-4">Hakuna miamala ya kuonyesha.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold uppercase text-sm"><i class="fas fa-bullseye mr-1"></i> Goals</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Target</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentGoals as $goal)
                                            <tr>
                                                <td>{{ $goal->title }}</td>
                                                <td><span class="badge badge-info">{{ strtoupper($goal->status ?? '-') }}</span></td>
                                                <td class="font-weight-bold">{{ number_format($goal->target_amount, 0) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted p-3">Hakuna goals.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold uppercase text-sm"><i class="fas fa-coins mr-1"></i> Budgets</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Month</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentBudgets as $b)
                                            <tr>
                                                <td>{{ $b->category }}</td>
                                                <td>{{ str_pad((string) $b->month, 2, '0', STR_PAD_LEFT) }}/{{ $b->year }}</td>
                                                <td class="font-weight-bold">{{ number_format($b->amount, 0) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted p-3">Hakuna budgets.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold uppercase text-sm">
                        <i class="fas fa-hand-holding-usd mr-1"></i> Debts
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Person</th>
                                    <th>Amount</th>
                                    <th>Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDebts as $d)
                                    <tr>
                                        <td><span class="badge badge-secondary">{{ strtoupper($d->type) }}</span></td>
                                        <td>{{ $d->person_name }}</td>
                                        <td class="font-weight-bold">{{ number_format($d->amount, 0) }}</td>
                                        <td>{{ optional($d->due_date)->format('d/m/Y') ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $d->is_paid ? 'badge-success' : 'badge-warning' }}">
                                                {{ $d->is_paid ? 'PAID' : 'UNPAID' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted p-3">Hakuna debts.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
