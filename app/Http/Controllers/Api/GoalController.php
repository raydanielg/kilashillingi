<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min(100, $perPage));

        $status = $request->query('status');
        $status = is_string($status) ? strtolower(trim($status)) : null;
        if (! in_array($status, ['active', 'completed', 'paused'], true)) {
            $status = null;
        }

        $q = $request->user()
            ->goals()
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'paused' THEN 1 ELSE 2 END")
            ->orderByRaw("due_date IS NULL")
            ->orderBy('due_date', 'asc')
            ->orderBy('id', 'desc');

        if ($status) {
            $q->where('status', $status);
        }

        return response()->json($q->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,completed,paused'],
        ]);

        $goal = $request->user()->goals()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'target_amount' => $validated['target_amount'],
            'current_amount' => $validated['current_amount'] ?? 0,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return response()->json([
            'goal' => $goal,
        ], 201);
    }

    public function update(Request $request, Goal $goal): JsonResponse
    {
        if ((int) $goal->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'target_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'current_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,completed,paused'],
        ]);

        $goal->fill($validated);

        // Auto-mark as completed if current >= target and status is still active
        $target = (float) ($goal->target_amount ?? 0);
        $current = (float) ($goal->current_amount ?? 0);
        if ($target > 0 && $current >= $target && ($goal->status ?? 'active') === 'active') {
            $goal->status = 'completed';
        }

        $goal->save();

        return response()->json([
            'goal' => $goal,
        ]);
    }

    public function destroy(Request $request, Goal $goal): JsonResponse
    {
        if ((int) $goal->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $goal->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
