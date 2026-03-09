<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function create(): View
    {
        $recentExpenses = Auth::user()
            ->transactions()
            ->where('type', 'expense')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('user.expenses.create', compact('recentExpenses'));
    }
}
