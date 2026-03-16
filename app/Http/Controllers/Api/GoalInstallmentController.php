<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\GoalInstallment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoalInstallmentController extends Controller
{
    public function index(Request $request, Goal $goal): JsonResponse
    {
        if ((int) $goal->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $items = $goal->installments()
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get(['id', 'amount', 'date', 'note', 'created_at']);

        return response()->json([
            'goal_id' => (int) $goal->id,
            'installments' => $items,
            'totals' => [
                'current_amount' => (float) $items->sum('amount'),
            ],
        ]);
    }

    public function store(Request $request, Goal $goal): JsonResponse
    {
        if ((int) $goal->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $tz = 'Africa/Dar_es_Salaam';
        $date = (string) ($validated['date'] ?? Carbon::now($tz)->toDateString());

        $installment = $goal->installments()->create([
            'user_id' => $request->user()->id,
            'amount' => $validated['amount'],
            'date' => $date,
            'note' => $validated['note'] ?? null,
        ]);

        // Recalculate current_amount from installments for consistency
        $newCurrent = (float) $goal->installments()->sum('amount');
        $goal->current_amount = $newCurrent;

        $target = (float) ($goal->target_amount ?? 0);
        if ($target > 0 && $newCurrent >= $target) {
            $goal->status = 'completed';
        } elseif (($goal->status ?? 'active') === 'completed' && ($target <= 0 || $newCurrent < $target)) {
            $goal->status = 'active';
        }

        $goal->save();

        return response()->json([
            'installment' => $installment,
            'goal' => $goal,
        ], 201);
    }

    public function destroy(Request $request, GoalInstallment $installment): JsonResponse
    {
        if ((int) $installment->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $goal = $installment->goal;

        $installment->delete();

        if ($goal && (int) $goal->user_id === (int) $request->user()->id) {
            $newCurrent = (float) $goal->installments()->sum('amount');
            $goal->current_amount = $newCurrent;

            $target = (float) ($goal->target_amount ?? 0);
            if ($target > 0 && $newCurrent >= $target) {
                $goal->status = 'completed';
            } elseif (($goal->status ?? 'active') === 'completed' && ($target <= 0 || $newCurrent < $target)) {
                $goal->status = 'active';
            }

            $goal->save();
        }

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
