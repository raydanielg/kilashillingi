<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Auth::user()->transactions()->orderBy('date', 'desc')->paginate(20);
        return view('transactions.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $description = (string) $request->input('description', '');
        $category = (string) $request->input('category', '');
        $source = (string) $request->input('source', '');

        if ($request->input('type') === 'expense' && $category === '__custom__') {
            $category = (string) $request->input('category_custom', '');
        }

        if ($request->input('type') === 'income' && $source === '__custom__') {
            $source = (string) $request->input('source_custom', '');
        }

        if ($request->input('type') === 'expense' && $category !== '') {
            $description = trim($category.' - '.$description, " -");
        }

        if ($request->input('type') === 'income' && $source !== '') {
            $description = trim($source.' - '.$description, " -");
        }

        Auth::user()->transactions()->create([
            'type' => $request->input('type'),
            'amount' => $request->input('amount'),
            'description' => $description,
            'date' => $request->input('date'),
        ]);

        return redirect()->back()->with('success', __('messages.save_success'));
    }
}
