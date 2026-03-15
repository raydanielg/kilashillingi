<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Debt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return view('welcome');
        }

        $transactions = $user->transactions()->orderBy('date', 'desc')->take(10)->get();
        $debts = $user->debts()->where('is_paid', false)->orderBy('due_date', 'asc')->get();
        
        $totalIncome = $user->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $monthStart = Carbon::now()->startOfMonth();
        $monthIncome = $user->transactions()->where('type', 'income')->where('date', '>=', $monthStart)->sum('amount');
        $monthExpense = $user->transactions()->where('type', 'expense')->where('date', '>=', $monthStart)->sum('amount');

        $tz = 'Africa/Dar_es_Salaam';
        $today = Carbon::now($tz)->toDateString();
        $yesterday = Carbon::now($tz)->subDay()->toDateString();

        $todayIncome = $user->transactions()->where('type', 'income')->whereDate('date', $today)->sum('amount');
        $todayExpense = $user->transactions()->where('type', 'expense')->whereDate('date', $today)->sum('amount');
        $yesterdayExpense = $user->transactions()->where('type', 'expense')->whereDate('date', $yesterday)->sum('amount');

        $todayTransactionsCount = $user->transactions()->whereDate('date', $today)->count();
        $monthTransactionsCount = $user->transactions()->where('date', '>=', $monthStart->toDateString())->count();

        $expenseTrendDirection = $todayExpense >= $yesterdayExpense ? 'up' : 'down';
        $expenseTrendPercent = $yesterdayExpense > 0
            ? (($todayExpense - $yesterdayExpense) / $yesterdayExpense) * 100
            : ($todayExpense > 0 ? 100 : 0);

        $trendStart = Carbon::now($tz)->subDays(6)->startOfDay();
        $trendDates = collect(range(0, 6))
            ->map(fn ($i) => Carbon::now($tz)->subDays(6 - $i)->toDateString());

        $trendRows = $user->transactions()
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

        $trendMap = $trendRows->mapWithKeys(fn ($row) => [(string) $row->d => (float) $row->total]);
        $incomeTrendMap = $incomeTrendRows->mapWithKeys(fn ($row) => [(string) $row->d => (float) $row->total]);
        $dailyExpenseLabels = $trendDates->map(fn ($d) => Carbon::parse($d)->format('d/m'))->values();
        $dailyExpenseData = $trendDates->map(fn ($d) => (float) ($trendMap[$d] ?? 0))->values();
        $dailyIncomeData = $trendDates->map(fn ($d) => (float) ($incomeTrendMap[$d] ?? 0))->values();

        $knownCategories = [
            'Chakula',
            'Usafiri',
            'Kodi ya Nyumba',
            'Umeme & Maji',
            'Huduma za Simu',
            'Burudani',
            'Afya',
            'Elimu',
            'Mengineyo',
        ];

        $expenseRows = $user->transactions()
            ->where('type', 'expense')
            ->where('date', '>=', $monthStart->toDateString())
            ->get(['description', 'amount']);

        $categoryTotals = array_fill_keys($knownCategories, 0.0);
        foreach ($expenseRows as $row) {
            $desc = (string) ($row->description ?? '');
            $amount = (float) $row->amount;

            $cat = null;
            foreach ($knownCategories as $candidate) {
                if ($desc === $candidate || str_starts_with($desc, $candidate.' -')) {
                    $cat = $candidate;
                    break;
                }
            }

            $categoryTotals[$cat ?? 'Mengineyo'] = ($categoryTotals[$cat ?? 'Mengineyo'] ?? 0) + $amount;
        }

        $pieLabels = [];
        $pieData = [];
        foreach ($categoryTotals as $cat => $total) {
            if ($total <= 0) {
                continue;
            }
            $pieLabels[] = $cat;
            $pieData[] = round($total, 2);
        }

        return view('dashboard', compact(
            'transactions',
            'debts',
            'totalIncome',
            'totalExpense',
            'balance',
            'monthIncome',
            'monthExpense',
            'todayIncome',
            'todayExpense',
            'yesterdayExpense',
            'todayTransactionsCount',
            'monthTransactionsCount',
            'expenseTrendDirection',
            'expenseTrendPercent',
            'dailyExpenseLabels',
            'dailyExpenseData',
            'dailyIncomeData',
            'pieLabels',
            'pieData',
        ));
    }
}
