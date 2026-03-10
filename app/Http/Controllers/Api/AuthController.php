<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'regex:/^0[67]\d{8}$/', 'unique:' . User::class],
            'currency' => ['nullable', 'string', 'max:10'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $phone = preg_replace('/[^0-9]/', '', (string) $validated['phone']);
        if (str_starts_with($phone, '0')) {
            $phone = '255' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '6')) {
            $phone = '255' . $phone;
        }

        $currency = $validated['currency'] ?? Setting::get('currency', 'KSh');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $phone,
            'role' => 'user',
            'currency' => $currency,
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim((string) $validated['login']);

        /** @var \App\Models\User|null $user */
        $user = null;

        if (str_contains($login, '@')) {
            $user = User::where('email', $login)->first();
        } else {
            $phone = preg_replace('/[^0-9]/', '', $login);
            if (str_starts_with($phone, '0')) {
                $phone = '255' . substr($phone, 1);
            } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '6')) {
                $phone = '255' . $phone;
            }

            $user = User::where('phone', $phone)->first();
        }

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 422);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out.',
        ]);
    }
}
