<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $goals = $user->goals()
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'paused' THEN 1 ELSE 2 END")
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('user.goals.index', compact('goals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
        ]);

        $goal = $request->user()->goals()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'target_amount' => $validated['target_amount'],
            'current_amount' => $validated['current_amount'] ?? 0,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => 'active',
        ]);

        $target = (float) ($goal->target_amount ?? 0);
        $current = (float) ($goal->current_amount ?? 0);
        if ($target > 0 && $current >= $target) {
            $goal->status = 'completed';
            $goal->save();
        }

        return redirect()->route('goals.index')->with('success', 'Lengo limehifadhiwa vizuri.');
    }

    public function update(Request $request, Goal $goal)
    {
        if ((int) $goal->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,completed,paused'],
        ]);

        $goal->update($validated);

        return redirect()->route('goals.index')->with('success', 'Lengo limesasishwa.');
    }

    public function progress(Request $request, Goal $goal)
    {
        if ((int) $goal->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'current_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $goal->current_amount = $validated['current_amount'];

        $target = (float) ($goal->target_amount ?? 0);
        $current = (float) ($goal->current_amount ?? 0);

        if ($target > 0 && $current >= $target) {
            $goal->status = 'completed';
        } elseif (($goal->status ?? 'active') === 'completed') {
            $goal->status = 'active';
        }

        $goal->save();

        return redirect()->back()->with('success', 'Progress imesasishwa.');
    }

    public function toggleStatus(Request $request, Goal $goal)
    {
        if ((int) $goal->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:active,completed,paused'],
        ]);

        $goal->status = $validated['status'];
        $goal->save();

        return redirect()->back()->with('success', 'Hali ya lengo imebadilishwa.');
    }

    public function destroy(Goal $goal)
    {
        if ((int) $goal->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $goal->delete();

        return redirect()->back()->with('success', 'Lengo limefutwa.');
    }
}
