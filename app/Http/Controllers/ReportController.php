<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('user.reports.index');
    }

    public function generate(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'today');
        $date = Carbon::now('Africa/Dar_es_Salaam');
        
        $query = $user->transactions();
        $dailyReports = [];

        if ($type === 'today') {
            $query->whereDate('date', $date->toDateString());
            $title = "RIPOTI YA MIAMALA YA LEO - " . $date->format('d/m/Y');
        } elseif ($type === 'week') {
            $startDate = $date->copy()->subDays(6);
            $query->where('date', '>=', $startDate->toDateString());
            $title = "RIPOTI YA MIAMALA YA WIKI - " . $startDate->format('d/m/Y') . " MPAKA " . $date->format('d/m/Y');
            
            // Group transactions by date for weekly view
            $allTransactions = $query->orderBy('date', 'asc')->orderBy('created_at', 'asc')->get();
            $grouped = $allTransactions->groupBy(function($tx) {
                return Carbon::parse($tx->date)->format('Y-m-d');
            });

            for ($i = 0; $i < 7; $i++) {
                $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
                if (isset($grouped[$currentDate])) {
                    $dayTxs = $grouped[$currentDate];
                    $dayIncome = $dayTxs->where('type', 'income')->sum('amount');
                    $dayExpense = $dayTxs->where('type', 'expense')->sum('amount');
                    $dayBalance = $dayIncome - $dayExpense;
                    
                    $dailyReports[] = [
                        'date' => Carbon::parse($currentDate)->format('d/m/Y'),
                        'transactions' => $dayTxs,
                        'income' => $dayIncome,
                        'expense' => $dayExpense,
                        'balance' => $dayBalance,
                        'comment' => $this->getComment($dayIncome, $dayExpense)
                    ];
                }
            }
        } elseif ($type === 'month') {
            $query->whereMonth('date', $date->month)
                  ->whereYear('date', $date->year);
            $title = "RIPOTI YA MIAMALA YA MWEZI - " . $date->format('F Y');
        } else {
            $query->whereDate('date', $date->toDateString());
            $title = "RIPOTI YA MIAMALA - " . $date->format('d/m/Y');
        }

        $transactions = $query->orderBy('created_at', 'asc')->get();
        $income = $transactions->where('type', 'income');
        $expense = $transactions->where('type', 'expense');
        
        $totalIncome = $income->sum('amount');
        $totalExpense = $expense->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Pie chart data (Category breakdown)
        $knownCategories = ['Chakula','Usafiri','Kodi ya Nyumba','Umeme & Maji','Huduma za Simu','Burudani','Afya','Elimu','Mengineyo'];
        $categoryTotals = array_fill_keys($knownCategories, 0.0);
        
        foreach ($expense as $tx) {
            $desc = (string) ($tx->description ?? '');
            $catFound = 'Mengineyo';
            foreach ($knownCategories as $candidate) {
                if (str_starts_with($desc, $candidate.' -')) {
                    $catFound = $candidate;
                    break;
                }
            }
            $categoryTotals[$catFound] += (float)$tx->amount;
        }

        $pieLabels = [];
        $pieData = [];
        foreach ($categoryTotals as $cat => $total) {
            if ($total > 0) {
                $pieLabels[] = $cat;
                $pieData[] = $total;
            }
        }

        $comment = $this->getComment($totalIncome, $totalExpense);

        return view('user.reports.preview', compact(
            'transactions', 
            'income', 
            'expense', 
            'totalIncome', 
            'totalExpense', 
            'balance',
            'title',
            'type',
            'comment',
            'pieLabels',
            'pieData',
            'dailyReports'
        ));
    }

    private function getComment($totalIncome, $totalExpense)
    {
        $balance = $totalIncome - $totalExpense;
        if ($totalIncome == 0 && $totalExpense == 0) {
            return "Hakuna miamala iliyofanyika. Anza kurekodi mapato na matumizi yako kwa usimamizi mzuri.";
        } elseif ($balance > 0) {
            $percentSaved = ($totalIncome > 0) ? ($balance / $totalIncome) * 100 : 100;
            if ($percentSaved > 30) {
                return "Mwenendo mzuri sana! Umefanikiwa kuokoa kiasi kikubwa cha mapato yako. Endelea na nidhamu hii.";
            } else {
                return "Mwenendo wa kuridhisha. Umeweza kubaki na salio, jitahidi kupunguza matumizi kuongeza akiba.";
            }
        } elseif ($balance == 0 && $totalIncome > 0) {
            return "Matumizi ni sawa na mapato. Hali hii haina akiba ya dharura. Jaribu kupunguza matumizi.";
        } else {
            return "Tahadhari! Matumizi yamezidi mapato. Unashauriwa kupunguza matumizi mara moja.";
        }
    }
}
