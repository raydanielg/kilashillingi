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

        $tz = 'Africa/Dar_es_Salaam';
        $today = Carbon::now($tz)->toDateString();
        $monthStart = Carbon::now($tz)->startOfMonth();

        $transactions = $user->transactions()
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get(['id', 'type', 'amount', 'description', 'date']);

        $totalIncome = (float) $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = (float) $user->transactions()->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $monthIncome = (float) $user->transactions()->where('type', 'income')->where('date', '>=', $monthStart)->sum('amount');
        $monthExpense = (float) $user->transactions()->where('type', 'expense')->where('date', '>=', $monthStart)->sum('amount');

        $todayIncome = (float) $user->transactions()->where('type', 'income')->whereDate('date', $today)->sum('amount');
        $todayExpense = (float) $user->transactions()->where('type', 'expense')->whereDate('date', $today)->sum('amount');
        $todayTransactionsCount = (int) $user->transactions()->whereDate('date', $today)->count();
        $monthTransactionsCount = (int) $user->transactions()->where('date', '>=', $monthStart->toDateString())->count();

        $trendStart = Carbon::now($tz)->subDays(6)->startOfDay();
        $trendDates = collect(range(0, 6))
            ->map(fn ($i) => Carbon::now($tz)->subDays(6 - $i)->toDateString());

        $expenseTrendRows = $user->transactions()
            ->selectRaw('date as d, SUM(amount) as total')
            ->where('type', 'expense')
            ->where('date', '>=', $trendStart->toDateString())
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $incomeTrendRows = $user->transactions()
            ->selectRaw('date as d, SUM(amount) as total')
            ->where('type', 'income')
            ->where('date', '>=', $trendStart->toDateString())
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $expenseTrendMap = $expenseTrendRows->mapWithKeys(fn ($row) => [(string) $row->d => (float) $row->total]);
        $incomeTrendMap = $incomeTrendRows->mapWithKeys(fn ($row) => [(string) $row->d => (float) $row->total]);

        $trendLabels = $trendDates->map(fn ($d) => Carbon::parse($d)->format('d/m'))->values();
        $trendExpense = $trendDates->map(fn ($d) => (float) ($expenseTrendMap[$d] ?? 0))->values();
        $trendIncome = $trendDates->map(fn ($d) => (float) ($incomeTrendMap[$d] ?? 0))->values();

        $recent = $transactions->map(function ($t) {
            return [
                'id' => (int) $t->id,
                'type' => (string) $t->type,
                'amount' => (float) $t->amount,
                'description' => (string) ($t->description ?? ''),
                'date' => $t->date ? Carbon::parse($t->date)->toDateString() : null,
            ];
        })->values();

        return response()->json([
            'currency' => $user->currency ?? 'TSh',
            'totals' => [
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'balance' => $balance,
                'month_income' => $monthIncome,
                'month_expense' => $monthExpense,
                'today_income' => $todayIncome,
                'today_expense' => $todayExpense,
                'today_transactions_count' => $todayTransactionsCount,
                'month_transactions_count' => $monthTransactionsCount,
            ],
            'trends' => [
                'labels' => $trendLabels,
                'income' => $trendIncome,
                'expense' => $trendExpense,
            ],
            'recent_transactions' => $recent,
        ]);
    }
}
