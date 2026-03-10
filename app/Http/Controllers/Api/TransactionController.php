<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min(100, $perPage));

        $transactions = $request->user()
            ->transactions()
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        return response()->json($transactions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'category' => ['nullable', 'string', 'max:100'],
            'category_custom' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:100'],
            'source_custom' => ['nullable', 'string', 'max:100'],
        ]);

        $description = (string) ($validated['description'] ?? '');
        $category = (string) ($validated['category'] ?? '');
        $source = (string) ($validated['source'] ?? '');

        if (($validated['type'] ?? '') === 'expense' && $category === '__custom__') {
            $category = (string) ($validated['category_custom'] ?? '');
        }

        if (($validated['type'] ?? '') === 'income' && $source === '__custom__') {
            $source = (string) ($validated['source_custom'] ?? '');
        }

        if (($validated['type'] ?? '') === 'expense' && $category !== '') {
            $description = trim($category . ' - ' . $description, " -");
        }

        if (($validated['type'] ?? '') === 'income' && $source !== '') {
            $description = trim($source . ' - ' . $description, " -");
        }

        $tx = $request->user()->transactions()->create([
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $description,
            'date' => $validated['date'],
        ]);

        return response()->json([
            'transaction' => $tx,
        ], 201);
    }

    public function destroy(Request $request, Transaction $transaction): JsonResponse
    {
        if ((int) $transaction->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
