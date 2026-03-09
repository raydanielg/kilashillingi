@extends('adminlte::page')

@section('title', 'Admin - Matangazo')

@section('content_header')
    <h1>Usimamizi wa Matangazo</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-5">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold uppercase">Tunga Tangazo Jipya</h3>
                </div>
                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Kichwa cha Habari</label>
                            <input type="text" name="title" class="form-control" placeholder="Mf: Maboresho ya Mfumo" required>
                        </div>
                        <div class="form-group">
                            <label>Ujumbe</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Andika ujumbe wako hapa..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Aina ya Tangazo</label>
                            <select name="type" class="form-control" required>
                                <option value="info">Taarifa (Blue)</option>
                                <option value="success">Mafanikio (Green)</option>
                                <option value="warning">Tahadhari (Yellow)</option>
                                <option value="danger">Hatari (Red)</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                            <i class="fas fa-paper-plane mr-1"></i> CHAPISHA SASA
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold uppercase">Matangazo Yaliyopita</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tangazo</th>
                                    <th>Aina</th>
                                    <th>Tarehe</th>
                                    <th>Hatua</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($announcements as $ann)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-sm">{{ $ann->title }}</div>
                                        <div class="text-xs text-muted">{{ Str::limit($ann->message, 50) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $ann->type }} text-uppercase">
                                            {{ $ann->type }}
                                        </span>
                                    </td>
                                    <td class="text-xs">{{ $ann->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Je, una uhakika unataka kufuta tangazo hili?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Hakuna matangazo yaliyochapishwa bado.</td>
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
