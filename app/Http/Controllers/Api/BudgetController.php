<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();
        $now = Carbon::now('Africa/Dar_es_Salaam');
        $month = $now->month;
        $year = $now->year;

        $budgets = $user->budgets()
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $monthStart = $now->copy()->startOfMonth();
        $transactions = $user->transactions()
            ->where('type', 'expense')
            ->where('date', '>=', $monthStart->toDateString())
            ->get();

        $knownCategories = ['Chakula', 'Usafiri', 'Kodi ya Nyumba', 'Umeme & Maji', 'Huduma za Simu', 'Burudani', 'Afya', 'Elimu', 'Mengineyo'];

        $budgetData = [];
        $totalBudgetLimit = 0.0;
        $totalActualSpent = 0.0;

        foreach ($knownCategories as $cat) {
            $budget = $budgets->where('category', $cat)->first();
            $limit = (float) ($budget->amount ?? 0);

            $spent = (float) $transactions
                ->filter(fn ($tx) => str_starts_with((string) $tx->description, $cat . ' -') || (string) $tx->description === $cat)
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
                    'is_over' => $spent > $limit && $limit > 0,
                ];

                $totalBudgetLimit += $limit;
                $totalActualSpent += $spent;
            }
        }

        return response()->json([
            'month' => $month,
            'year' => $year,
            'known_categories' => $knownCategories,
            'budget_data' => $budgetData,
            'totals' => [
                'limit' => $totalBudgetLimit,
                'spent' => $totalActualSpent,
                'remaining' => max(0, $totalBudgetLimit - $totalActualSpent),
            ],
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $now = Carbon::now('Africa/Dar_es_Salaam');
        $month = (int) ($validated['month'] ?? $now->month);
        $year = (int) ($validated['year'] ?? $now->year);

        $budget = $request->user()->budgets()->updateOrCreate(
            [
                'category' => $validated['category'],
                'month' => $month,
                'year' => $year,
            ],
            ['amount' => $validated['amount']]
        );

        return response()->json([
            'budget' => $budget,
        ]);
    }
}
