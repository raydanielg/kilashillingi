<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(Request $request, User $user)
    {
        $tz = 'Africa/Dar_es_Salaam';
        $now = Carbon::now($tz);

        $incomeTotal = (float) $user->transactions()->where('type', 'income')->sum('amount');
        $expenseTotal = (float) $user->transactions()->where('type', 'expense')->sum('amount');
        $balanceTotal = $incomeTotal - $expenseTotal;

        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthEnd = $now->copy()->endOfMonth()->toDateString();

        $incomeMonth = (float) $user->transactions()
            ->where('type', 'income')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');
        $expenseMonth = (float) $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');
        $balanceMonth = $incomeMonth - $expenseMonth;

        $transactionsCount = (int) $user->transactions()->count();
        $budgetsCount = (int) $user->budgets()->count();
        $goalsCount = (int) $user->goals()->count();
        $debtsCount = (int) $user->debts()->count();

        $recentTransactions = $user->transactions()->orderBy('date', 'desc')->limit(10)->get();
        $recentBudgets = $user->budgets()->orderBy('year', 'desc')->orderBy('month', 'desc')->limit(10)->get();
        $recentGoals = $user->goals()->orderBy('created_at', 'desc')->limit(10)->get();
        $recentDebts = $user->debts()->orderBy('is_paid', 'asc')->orderBy('due_date', 'asc')->limit(10)->get();

        return view('admin.users.show', compact(
            'user',
            'incomeTotal',
            'expenseTotal',
            'balanceTotal',
            'incomeMonth',
            'expenseMonth',
            'balanceMonth',
            'transactionsCount',
            'budgetsCount',
            'goalsCount',
            'debtsCount',
            'recentTransactions',
            'recentBudgets',
            'recentGoals',
            'recentDebts'
        ));
    }
}
