@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <h1>Admin Dashboard</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <p class="mb-3">Karibu kwenye Admin Panel ya KilaShillingi.</p>

            <a href="{{ route('dashboard') }}" class="btn btn-success">Rudi User Dashboard</a>
        </div>
    </div>
@stop
