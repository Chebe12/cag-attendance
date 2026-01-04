<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Show the settings page
     */
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    /**
     * Update user preferences/settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'notification_email' => 'nullable|boolean',
            'notification_sms' => 'nullable|boolean',
            'notification_push' => 'nullable|boolean',
            'language' => 'nullable|in:en,es,fr',
            'timezone' => 'nullable|string|max:50',
        ]);

        // Update user settings/preferences
        // For now, we'll store in session or you can add a settings column/table
        session()->put('user_settings', $validated);

        return redirect()
            ->route('settings')
            ->with('success', 'Settings updated successfully!');
    }
}
