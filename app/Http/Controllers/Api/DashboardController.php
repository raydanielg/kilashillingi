<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();

        $transactions = $user->transactions()->orderBy('date', 'desc')->take(10)->get();

        $totalIncome = (float) $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = (float) $user->transactions()->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $monthStart = Carbon::now()->startOfMonth();
        $monthIncome = (float) $user->transactions()->where('type', 'income')->where('date', '>=', $monthStart)->sum('amount');
        $monthExpense = (float) $user->transactions()->where('type', 'expense')->where('date', '>=', $monthStart)->sum('amount');

        return response()->json([
            'currency' => $user->currency,
            'totals' => [
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'balance' => $balance,
                'month_income' => $monthIncome,
                'month_expense' => $monthExpense,
            ],
            'recent_transactions' => $transactions,
        ]);
    }
}
