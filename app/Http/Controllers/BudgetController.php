<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now('Africa/Dar_es_Salaam');
        $month = $now->month;
        $year = $now->year;

        // Fetch user budgets for the current month
        $budgets = $user->budgets()
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Calculate spending per category for this month
        $monthStart = $now->copy()->startOfMonth();
        $transactions = $user->transactions()
            ->where('type', 'expense')
            ->where('date', '>=', $monthStart->toDateString())
            ->get();

        $knownCategories = ['Chakula', 'Usafiri', 'Kodi ya Nyumba', 'Umeme & Maji', 'Huduma za Simu', 'Burudani', 'Afya', 'Elimu', 'Mengineyo'];
        
        $budgetData = [];
        $totalBudgetLimit = 0;
        $totalActualSpent = 0;

        foreach ($knownCategories as $cat) {
            $budget = $budgets->where('category', $cat)->first();
            $limit = $budget->amount ?? 0;
            
            // Sum transactions that start with this category
            $spent = $transactions->filter(fn($tx) => str_starts_with($tx->description, $cat . ' -') || $tx->description === $cat)
                ->sum('amount');

            if ($limit > 0 || $spent > 0) {
                $percent = $limit > 0 ? ($spent / $limit) * 100 : ($spent > 0 ? 100 : 0);
                $budgetData[] = [
                    'id' => $budget->id ?? null,
                    'category' => $cat,
                    'limit' => $limit,
                    'spent' => $spent,
                    'remaining' => max(0, $limit - $spent),
                    'percent' => min(100, $percent),
                    'is_over' => $spent > $limit && $limit > 0
                ];
                $totalBudgetLimit += $limit;
                $totalActualSpent += $spent;
            }
        }

        return view('user.budget.index', compact('budgetData', 'totalBudgetLimit', 'totalActualSpent', 'knownCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $now = Carbon::now('Africa/Dar_es_Salaam');
        
        Auth::user()->budgets()->updateOrCreate(
            [
                'category' => $request->category,
                'month' => $now->month,
                'year' => $now->year,
            ],
            ['amount' => $request->amount]
        );

        return redirect()->route('budget.index')->with('success', 'Bajeti imehifadhiwa vizuri.');
    }
}
