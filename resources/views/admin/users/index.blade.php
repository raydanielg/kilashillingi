@extends('adminlte::page')

@section('title', 'Admin - Watumiaji')

@section('content_header')
    <h1>Usimamizi wa Watumiaji</h1>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Orodha ya Watumiaji</h3>
            <div class="card-tools">
                <form action="{{ route('admin.users') }}" method="GET" class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control float-right" placeholder="Tafuta..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Jina</th>
                            <th>Email</th>
                            <th>Simu</th>
                            <th>Role</th>
                            <th>Tarehe ya Kujiunga</th>
                            <th>Hatua</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $user->role === 'admin' ? 'badge-danger' : 'badge-success' }}">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <button class="btn btn-xs btn-info" title="Hariri"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-xs btn-danger" title="Funga Akaunti"><i class="fas fa-ban"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            {{ $users->links() }}
        </div>
    </div>
@stop
