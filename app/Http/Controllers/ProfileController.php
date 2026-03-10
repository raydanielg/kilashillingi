<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'string'], // Hii itakuwa base64 string kutoka kwa Cropper.js
        ]);

        $user = $request->user();
        
        // Futa avatar ya zamani kama ipo
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Shughulikia base64 image
        $image_data = $request->avatar;
        if (preg_match('/^data:image\/(\w+);base64,/', $image_data, $type)) {
            $image_data = substr($image_data, strpos($image_data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, etc

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                return back()->with('error', 'Aina ya picha haikubaliki.');
            }

            $image_data = base64_decode($image_data);

            if ($image_data === false) {
                return back()->with('error', 'Picha imeshindwa kusomwa.');
            }
        } else {
            return back()->with('error', 'Data za picha si sahihi.');
        }

        $fileName = 'avatars/' . $user->id . '_' . time() . '.' . $type;
        Storage::disk('public')->put($fileName, $image_data);

        $user->avatar = $fileName;
        $user->save();

        return back()->with('success', 'Picha ya wasifu imesasishwa kikamilifu.');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldCurrency = $user->currency;
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Ikiwa amebadilisha currency, futa data zote zilizopita
        if ($user->isDirty('currency') && $oldCurrency !== null) {
            $user->transactions()->delete();
            $user->budgets()->delete();
            $user->debts()->delete();
            $user->reminders()->delete();
            
            $user->save();
            return Redirect::route('profile.edit')->with('status', 'profile-updated-and-cleared');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
