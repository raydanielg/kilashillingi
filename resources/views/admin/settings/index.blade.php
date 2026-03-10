@extends('adminlte::page')

@section('title', 'Mipangilio ya Mfumo')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-cogs mr-2"></i>Mipangilio ya Mfumo</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-check"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card card-outline card-emerald shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-app mr-1"></i> Taarifa za Programu (App Info)
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_name">Jina la Programu</label>
                                    <input type="text" name="app_name" id="app_name" class="form-control form-control-lg" 
                                           value="{{ $settings['app_name'] ?? config('app.name') }}" placeholder="Mfano: KilaShillingi">
                                    <small class="text-muted">Jina hili litaonekana kwenye kichwa cha ukurasa na barua pepe.</small>
                                </div>

                                <div class="form-group mt-4">
                                    <label for="app_logo">Logo ya Programu</label>
                                    <div class="custom-file">
                                        <input type="file" name="app_logo" class="custom-file-input" id="app_logo" accept="image/*">
                                        <label class="custom-file-label" for="app_logo">Chagua picha...</label>
                                    </div>
                                    <small class="text-muted">Saizi inayopendekezwa: 512x512px (PNG/JPG).</small>
                                </div>
                            </div>
                            <div class="col-md-6 text-center d-flex align-items-center justify-content-center border-left">
                                <div class="p-3 bg-light rounded shadow-sm" style="min-width: 200px;">
                                    <p class="text-sm font-weight-bold text-uppercase mb-2">Logo ya Sasa</p>
                                    @if(isset($settings['app_logo']))
                                        <img src="{{ asset('storage/' . $settings['app_logo']) }}" alt="App Logo" class="img-fluid rounded shadow-sm" style="max-height: 120px;">
                                    @else
                                        <div class="py-4 text-muted">
                                            <i class="fas fa-image fa-4x mb-2"></i>
                                            <p>Hakuna Logo</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card card-outline card-warning shadow-sm h-100">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-tools mr-1"></i> Maintenance Mode
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                        <input type="checkbox" name="maintenance_mode" class="custom-control-input" id="maintenance_mode" 
                                               {{ (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="maintenance_mode">Washa Hali ya Matengenezo</label>
                                    </div>
                                    <p class="text-muted small mt-2">
                                        Ikiooshwa, watumiaji wa kawaida hawataweza kutumia mfumo isipokuwa ma-admin pekee.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-outline card-info shadow-sm h-100">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-coins mr-1"></i> Fedha na Maeneo
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="currency">Alama ya Fedha (Currency)</label>
                                    <input type="text" name="currency" id="currency" class="form-control" 
                                           value="{{ $settings['currency'] ?? 'TSh' }}" placeholder="Mfano: TSh, $, Ksh">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary shadow-sm mt-4">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-address-book mr-1"></i> Mawasiliano na Footer
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email">Barua Pepe ya Mawasiliano</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" name="contact_email" id="contact_email" class="form-control" 
                                               value="{{ $settings['contact_email'] ?? '' }}" placeholder="support@kilashillingi.com">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_phone">Namba ya Simu ya Huduma</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="text" name="contact_phone" id="contact_phone" class="form-control" 
                                               value="{{ $settings['contact_phone'] ?? '' }}" placeholder="+255 ...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label for="footer_text">Maelezo ya Chini (Footer Text)</label>
                                    <textarea name="footer_text" id="footer_text" class="form-control" rows="2" 
                                              placeholder="Hakimiliki &copy; {{ date('Y') }} KilaShillingi. Haki zote zimehifadhiwa.">{{ $settings['footer_text'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <button type="submit" class="btn btn-emerald btn-lg px-5 shadow-sm">
                            <i class="fas fa-save mr-2"></i> Hifadhi Mabadiliko
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .card-emerald:not(.card-outline) { background-color: #059669; }
        .card-outline.card-emerald { border-top: 3px solid #059669; }
        .btn-emerald { background-color: #059669; color: white; border: none; }
        .btn-emerald:hover { background-color: #047857; color: white; }
        .breadcrumb-item a { color: #059669; }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            // Update custom file input label
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@stop
