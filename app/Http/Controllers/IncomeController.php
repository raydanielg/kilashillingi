<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function create(): View
    {
        $recentIncome = Auth::user()
            ->transactions()
            ->where('type', 'income')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('user.income.create', compact('recentIncome'));
    }
}
