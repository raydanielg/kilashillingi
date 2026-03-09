<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index()
    {
        $now = Carbon::now('Africa/Dar_es_Salaam');
        $startOfWeek = $now->copy()->startOfWeek();
        
        $weeklyRegistrations = User::where('created_at', '>=', $startOfWeek)->count();
        $totalUsers = User::count();
        $totalTransactions = Transaction::count();
        
        // Data for registration chart (last 7 days)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i);
            $chartLabels[] = $d->format('D');
            $chartData[] = User::whereDate('created_at', $d->toDateString())->count();
        }

        return view('admin.statistics.index', compact('weeklyRegistrations', 'totalUsers', 'totalTransactions', 'chartLabels', 'chartData'));
    }
}
