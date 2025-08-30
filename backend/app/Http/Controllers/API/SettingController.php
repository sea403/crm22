<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Get current user's settings.
     */
    public function show()
    {
        $user = Auth::user();

        $settings = Setting::firstOrCreate(
            ['user_id' => $user->id]
        );

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update current user's settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_currency' => 'nullable|string|max:10',
            'currency_format' => 'nullable|string|max:20',
            'number_format' => 'nullable|string|max:20',
            'default_country' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:50',
            'start_day_of_week' => 'nullable|string|max:20',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|string|in:12,24',
            'fiscal_year_start' => 'nullable|string|max:20',
            'default_language' => 'nullable|string|max:5',
        ]);

        $user = Auth::user();

        $settings = Setting::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully.',
            'data' => $settings
        ]);
    }
}
