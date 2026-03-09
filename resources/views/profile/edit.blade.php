@extends('layouts.user')

@section('page_title', 'Profaili Yangu')
@section('page_subtitle', 'Hariri taarifa zako binafsi na usalama wa akaunti')

@section('content')
<div class="max-w-5xl space-y-6">
    <!-- Taarifa za Profaili -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
            <i class="fas fa-id-card text-emerald-600"></i>
            <h3 class="text-sm font-bold text-gray-900 uppercase">Hariri Taarifa Binafsi</h3>
        </div>
        <div class="p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    <!-- Badili Nywila -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
            <i class="fas fa-key text-emerald-600"></i>
            <h3 class="text-sm font-bold text-gray-900 uppercase">Badili Nywila (Password)</h3>
        </div>
        <div class="p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
</div>
@endsection
