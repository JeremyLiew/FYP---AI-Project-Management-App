<?php

namespace App\Http\Controllers\Web;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function updateSettings(Request $request)
{
    $validatedData = $request->validate([
        'timezone' => 'nullable|timezone',
        'theme' => 'nullable|in:light,dark',
        'time_format' => 'nullable|in:12h,24h', // Validate time format
        'date_format' => 'nullable|in:MM/DD/YYYY,DD/MM/YYYY,YYYY/MM/DD', // Validate date format
        'email_time' => 'nullable|in:Weekly,Monthly', // Validate report email time
    ]);

    $user = auth()->user();

    if (!$user) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    // Update the timezone if provided
    if (isset($validatedData['timezone'])) {
        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'timezone'],
            ['value' => $validatedData['timezone']]
        );
        config(['app.timezone' => $validatedData['timezone']]);
    }

    // Update the theme if provided
    if (isset($validatedData['theme'])) {
        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'theme'],
            ['value' => $validatedData['theme']]
        );
    }

    // Update the time format if provided
    if (isset($validatedData['time_format'])) {
        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'time_format'],
            ['value' => $validatedData['time_format']]
        );
    }

    // Update the date format if provided
    if (isset($validatedData['date_format'])) {
        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'date_format'],
            ['value' => $validatedData['date_format']]
        );
    }
    $email = $request->input('email_time');

    // Update the report email time if provided
    if (isset($validatedData['email_time'])) {
        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'email_time'],
            ['value' => $validatedData['email_time']]
        );
    }

    return response()->json(['message' => 'Settings updated successfully!']);
}

public function getUserSettings()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    $settings = Setting::where('user_id', $user->id)
        ->pluck('value', 'key')
        ->toArray();

    return response()->json($settings);
}
}
