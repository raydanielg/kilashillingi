<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Auth::user()->reminders()->orderBy('reminder_time', 'asc')->get();
        return view('user.reminders.index', compact('reminders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'reminder_time' => 'required',
            'frequency' => 'required|in:daily,weekly,monthly',
            'days' => 'nullable|array',
        ]);

        Auth::user()->reminders()->create([
            'title' => $request->title,
            'reminder_time' => $request->reminder_time,
            'frequency' => $request->frequency,
            'days' => $request->days,
            'is_active' => true,
        ]);

        return redirect()->route('reminders.index')->with('success', 'Kumbusho limehifadhiwa vizuri.');
    }

    public function toggle(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403);
        }

        $reminder->update(['is_active' => !$reminder->is_active]);

        return redirect()->route('reminders.index')->with('success', 'Hali ya kumbusho imebadilishwa.');
    }

    public function destroy(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403);
        }

        $reminder->delete();

        return redirect()->route('reminders.index')->with('success', 'Kumbusho limefutwa.');
    }
}
