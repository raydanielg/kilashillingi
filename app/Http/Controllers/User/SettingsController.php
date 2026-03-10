<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Debt;
use App\Models\Budget;
use App\Models\Reminder;

class SettingsController extends Controller
{
    public function index()
    {
        return view('user.settings.index');
    }

    public function resetData(Request $request)
    {
        $user = Auth::user();

        // Futa data zote za mtumiaji husika
        Transaction::where('user_id', $user->id)->delete();
        Debt::where('user_id', $user->id)->delete();
        Budget::where('user_id', $user->id)->delete();
        Reminder::where('user_id', $user->id)->delete();

        return redirect()->route('settings.index')->with('success', 'Data zote zimefutwa kikamilifu. Unaweza kuanza upya sasa.');
    }
}
