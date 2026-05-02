<?php

namespace App\Helpers;

use App\Models\CompanySetting;

class FaviconHelper
{
    /**
     * Get favicon URL from company settings
     * 
     * @return string
     */
    public static function getFaviconUrl()
    {
        try {
            $settings = CompanySetting::first();
            
            if ($settings && $settings->logo) {
                // Check if logo exists
                $logoPath = storage_path('app/public/' . $settings->logo);
                if (file_exists($logoPath)) {
                    return asset('storage/' . $settings->logo);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to get favicon from company settings: ' . $e->getMessage());
        }
        
        // Fallback to default logo
        $defaultLogo = 'WEB_HMTour/wp-content/uploads/2023/04/Logo-HM_UMRAH-3.png';
        if (file_exists(public_path($defaultLogo))) {
            return url($defaultLogo);
        }
        
        // Ultimate fallback
        return url('favicon.ico');
    }
    
    /**
     * Get favicon HTML tag
     * 
     * @return string
     */
    public static function getFaviconTag()
    {
        $url = self::getFaviconUrl();
        return '<link rel="icon" type="image/png" href="' . $url . '">';
    }
}
