<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $debts = $user->debts()->orderBy('is_paid', 'asc')->orderBy('due_date', 'asc')->get();
        
        $totalBorrowed = $user->debts()->where('type', 'borrowed')->where('is_paid', false)->sum('amount');
        $totalLent = $user->debts()->where('type', 'lent')->where('is_paid', false)->sum('amount');
        
        return view('debts.index', compact('debts', 'totalBorrowed', 'totalLent'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:lent,borrowed',
            'person_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:date',
        ]);

        Auth::user()->debts()->create($request->all());

        return redirect()->route('debts.index')->with('success', 'Deni limehifadhiwa vizuri.');
    }

    public function pay(Request $request, Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ]);

        // Logic for partial or full payment can be complex, 
        // for now we mark as paid and record a transaction
        $debt->update(['is_paid' => true]);

        Auth::user()->transactions()->create([
            'type' => $debt->type === 'borrowed' ? 'expense' : 'income',
            'amount' => $request->amount,
            'description' => ($debt->type === 'borrowed' ? 'Kulipa Deni: ' : 'Kupokea Deni: ') . $debt->person_name . ($request->note ? ' - ' . $request->note : ''),
            'date' => $request->payment_date,
        ]);

        return redirect()->route('debts.index')->with('success', 'Malipo yamefanikiwa kurekodiwa.');
    }

    public function markAsPaid(Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }

        $debt->update(['is_paid' => true]);

        return redirect()->back()->with('success', __('messages.paid_success'));
    }
}
