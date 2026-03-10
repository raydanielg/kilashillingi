<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^0[67]\d{8}$/',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'currency' => ['nullable', 'string', 'max:10'],
        ]);

        $oldCurrency = $user->currency;

        $phone = preg_replace('/[^0-9]/', '', (string) $validated['phone']);
        if (str_starts_with($phone, '0')) {
            $phone = '255' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '6')) {
            $phone = '255' . $phone;
        }

        $currency = $validated['currency'] ?? Setting::get('currency', 'KSh');

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $phone;
        $user->currency = $currency;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $cleared = false;
        if ($user->isDirty('currency') && $oldCurrency !== null) {
            $user->transactions()->delete();
            $user->budgets()->delete();
            $user->debts()->delete();
            $user->reminders()->delete();
            $cleared = true;
        }

        $user->save();

        return response()->json([
            'user' => $user,
            'cleared' => $cleared,
        ]);
    }
}
