<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:120'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $data['email'] = strtolower($data['email']);

        $user->fill($data)->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:10', 'confirmed'],
        ]);

        $user = $request->user();
        // Hashed by User::setPasswordAttribute — assign the plain value.
        $user->password = $request->string('new_password')->toString();
        $user->password_changed_at = now();
        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Password updated successfully.');
    }

    public function preferences(Request $request): View
    {
        return view('account.preferences', [
            'user' => $request->user(),
            'prefs' => $request->user()->prefs(),
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'currency' => ['required', Rule::in(['INR', 'USD', 'EUR', 'GBP', 'AED'])],
            'language' => ['required', Rule::in(['en', 'hi', 'gu'])],
            'temperature_unit' => ['required', Rule::in(['c', 'f'])],
            'distance_unit' => ['required', Rule::in(['km', 'mi'])],
        ]);

        // Checkboxes are the only reliable signal for on/off — an absent
        // checkbox means the user turned it off, so read them explicitly.
        foreach (['email_notifications', 'sms_notifications', 'whatsapp_notifications', 'deal_alerts', 'trip_reminders'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $user->preferences = $user->mergePreferences($data);
        $user->save();

        return Redirect::route('profile.preferences')->with('success', 'Preferences saved.');
    }
}
