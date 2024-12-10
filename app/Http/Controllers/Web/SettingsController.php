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
        'theme' => 'nullable|in:light,dark', // Ensure the theme is either 'light' or 'dark'
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
    }

    // Update the theme if provided
    if (isset($validatedData['theme'])) {
        Setting::updateOrCreate(
            ['user_id' => $user->id, 'key' => 'theme'],
            ['value' => $validatedData['theme']]
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
