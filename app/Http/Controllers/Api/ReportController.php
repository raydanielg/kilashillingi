<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function preview(Request $request): JsonResponse
    {
        $user = $request->user();

        $type = $request->query('type', 'today');
        $type = is_string($type) ? strtolower(trim($type)) : 'today';

        $tz = 'Africa/Dar_es_Salaam';
        $date = Carbon::now($tz);

        $query = $user->transactions();
        $dailyReports = [];

        if ($type === 'today') {
            $query->whereDate('date', $date->toDateString());
            $title = 'RIPOTI YA MIAMALA YA LEO - '.$date->format('d/m/Y');
        } elseif ($type === 'week') {
            $startDate = $date->copy()->subDays(6);
            $query->where('date', '>=', $startDate->toDateString());
            $title = 'RIPOTI YA MIAMALA YA WIKI - '.$startDate->format('d/m/Y').' MPAKA '.$date->format('d/m/Y');

            $allTransactions = $query->orderBy('date', 'asc')->orderBy('created_at', 'asc')->get();
            $grouped = $allTransactions->groupBy(function ($tx) {
                return Carbon::parse($tx->date)->format('Y-m-d');
            });

            for ($i = 0; $i < 7; $i++) {
                $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
                if (! isset($grouped[$currentDate])) {
                    continue;
                }

                $dayTxs = $grouped[$currentDate];
                $dayIncome = (float) $dayTxs->where('type', 'income')->sum('amount');
                $dayExpense = (float) $dayTxs->where('type', 'expense')->sum('amount');
                $dayBalance = $dayIncome - $dayExpense;

                $dailyReports[] = [
                    'date' => Carbon::parse($currentDate)->format('d/m/Y'),
                    'income' => $dayIncome,
                    'expense' => $dayExpense,
                    'balance' => $dayBalance,
                    'comment' => $this->getComment($dayIncome, $dayExpense),
                ];
            }
        } elseif ($type === 'month') {
            $query->whereMonth('date', $date->month)->whereYear('date', $date->year);
            $title = 'RIPOTI YA MIAMALA YA MWEZI - '.$date->format('F Y');
        } elseif ($type === 'year') {
            $query->whereYear('date', $date->year);
            $title = 'RIPOTI YA MIAMALA YA MWAKA - '.$date->format('Y');
        } else {
            $type = 'today';
            $query->whereDate('date', $date->toDateString());
            $title = 'RIPOTI YA MIAMALA YA LEO - '.$date->format('d/m/Y');
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $knownCategories = ['Chakula', 'Usafiri', 'Kodi ya Nyumba', 'Umeme & Maji', 'Huduma za Simu', 'Burudani', 'Afya', 'Elimu', 'Mengineyo'];
        $categoryTotals = array_fill_keys($knownCategories, 0.0);

        foreach ($transactions->where('type', 'expense') as $tx) {
            $desc = (string) ($tx->description ?? '');
            $catFound = 'Mengineyo';
            foreach ($knownCategories as $candidate) {
                if ($desc === $candidate || str_starts_with($desc, $candidate.' -')) {
                    $catFound = $candidate;
                    break;
                }
            }
            $categoryTotals[$catFound] = ($categoryTotals[$catFound] ?? 0) + (float) $tx->amount;
        }

        $pie = [];
        foreach ($categoryTotals as $cat => $total) {
            if ($total <= 0) {
                continue;
            }
            $pie[] = [
                'category' => $cat,
                'total' => (float) $total,
            ];
        }

        $comment = $this->getComment($totalIncome, $totalExpense);

        $items = $transactions->map(function ($tx) {
            return [
                'id' => (int) $tx->id,
                'type' => (string) $tx->type,
                'amount' => (float) $tx->amount,
                'description' => (string) ($tx->description ?? ''),
                'date' => (string) $tx->date,
                'time' => $tx->created_at ? $tx->created_at->format('H:i') : null,
                'created_at' => $tx->created_at ? $tx->created_at->toISOString() : null,
            ];
        })->values();

        return response()->json([
            'type' => $type,
            'title' => $title,
            'totals' => [
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'balance' => $balance,
            ],
            'comment' => $comment,
            'pie' => $pie,
            'daily_reports' => $dailyReports,
            'transactions' => $items,
        ]);
    }

    private function getComment($totalIncome, $totalExpense): string
    {
        $balance = $totalIncome - $totalExpense;

        if ($totalIncome == 0 && $totalExpense == 0) {
            return 'Hakuna miamala iliyofanyika. Anza kurekodi mapato na matumizi yako kwa usimamizi mzuri.';
        }

        if ($balance > 0) {
            $percentSaved = ($totalIncome > 0) ? ($balance / $totalIncome) * 100 : 100;
            if ($percentSaved > 30) {
                return 'Mwenendo mzuri sana! Umefanikiwa kuokoa kiasi kikubwa cha mapato yako. Endelea na nidhamu hii.';
            }
            return 'Mwenendo wa kuridhisha. Umeweza kubaki na salio, jitahidi kupunguza matumizi kuongeza akiba.';
        }

        if ($balance == 0 && $totalIncome > 0) {
            return 'Matumizi ni sawa na mapato. Hali hii haina akiba ya dharura. Jaribu kupunguza matumizi.';
        }

        return 'Tahadhari! Matumizi yamezidi mapato. Unashauriwa kupunguza matumizi mara moja.';
    }
}
