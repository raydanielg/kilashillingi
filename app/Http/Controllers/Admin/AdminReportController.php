<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    public function index()
    {
        $userStats = User::selectRaw('count(*) as count, role')
            ->groupBy('role')
            ->get();
            
        $transactionStats = Transaction::selectRaw('type, sum(amount) as total, count(*) as count')
            ->groupBy('type')
            ->get();

        return view('admin.reports.index', compact('userStats', 'transactionStats'));
    }
}
