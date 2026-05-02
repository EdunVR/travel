<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ResolutionSettingController extends Controller
{
    /**
     * Display resolution settings page
     */
    public function index()
    {
        return view('admin.sistem.resolusi.index');
    }

    /**
     * Save resolution settings
     */
    public function store(Request $request)
    {
        $request->validate([
            'scale' => 'required|numeric|min:50|max:150',
            'sidebar_width' => 'required|in:compact,normal,wide',
            'font_size' => 'required|in:small,normal,large',
            'spacing' => 'required|in:compact,normal,comfortable',
        ]);

        // Save to cookie (expires in 1 year)
        $settings = [
            'scale' => $request->scale,
            'sidebar_width' => $request->sidebar_width,
            'font_size' => $request->font_size,
            'spacing' => $request->spacing,
        ];

        Cookie::queue('resolution_settings', json_encode($settings), 525600); // 1 year

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan resolusi berhasil disimpan!',
            'settings' => $settings
        ]);
    }

    /**
     * Reset to default settings
     */
    public function reset()
    {
        Cookie::queue(Cookie::forget('resolution_settings'));

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan resolusi berhasil direset ke default!'
        ]);
    }

    /**
     * Get current settings
     */
    public function get()
    {
        $settings = request()->cookie('resolution_settings');
        
        if ($settings) {
            $settings = json_decode($settings, true);
        } else {
            // Default settings
            $settings = [
                'scale' => 100,
                'sidebar_width' => 'normal',
                'font_size' => 'normal',
                'spacing' => 'normal',
            ];
        }

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }
}
