<?php

namespace App\Traits;

use App\Models\CompanySetting;

trait HasCompanySettings
{
    /**
     * Get company settings for current outlet
     */
    public function getCompanySettings()
    {
        $outletId = $this->getCurrentOutletId();
        
        // If no outlet ID found, return null or throw exception
        if ($outletId === null) {
            // Return a default settings object to prevent errors
            return (object) [
                'company_name' => 'Nama Perusahaan',
                'company_code' => '',
                'company_address' => '',
                'company_phone' => '',
                'company_email' => '',
                'company_website' => '',
                'company_logo' => null,
                'logo_url' => null,
                'favicon_url' => null,
                'npwp' => '',
                'nib' => '',
                'siup' => '',
                'tdp' => '',
                'bank_name' => '',
                'bank_account_number' => '',
                'bank_account_name' => '',
                'currency' => 'IDR',
                'timezone' => 'Asia/Jakarta',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'tax_rate' => 11.00,
                'formatted_address' => '',
                'formatted_phone' => '',
            ];
        }
        
        return CompanySetting::getOrCreateForOutlet($outletId);
    }

    /**
     * Get current outlet ID
     */
    protected function getCurrentOutletId()
    {
        // Check if trait HasOutletFilter is used
        if (method_exists($this, 'getSelectedOutlet')) {
            $outletId = $this->getSelectedOutlet();
            
            // Handle string values like "ALL" or null
            if ($outletId === 'ALL' || $outletId === null || $outletId === '') {
                // Default to first accessible outlet
                if (method_exists($this, 'getAccessibleOutlets')) {
                    $outlets = $this->getAccessibleOutlets();
                    if ($outlets->isNotEmpty()) {
                        return (int) $outlets->first()->id_outlet;
                    }
                }
                // Fallback to first available outlet in database
                return $this->getFirstAvailableOutletId();
            }
            
            // Ensure we return an integer
            return (int) $outletId;
        }

        // Fallback to session or default
        $sessionOutletId = session('selected_outlet_id');
        
        // Handle string values from session
        if ($sessionOutletId === 'ALL' || $sessionOutletId === null || $sessionOutletId === '') {
            return $this->getFirstAvailableOutletId();
        }
        
        return (int) $sessionOutletId;
    }

    /**
     * Get first available outlet ID from database
     */
    protected function getFirstAvailableOutletId()
    {
        // Try to get from Outlet model
        if (class_exists('App\Models\Outlet')) {
            $outlet = \App\Models\Outlet::orderBy('id_outlet')->first();
            if ($outlet) {
                return (int) $outlet->id_outlet;
            }
        }
        
        // Fallback to direct DB query
        $outlet = \DB::table('outlets')->orderBy('id_outlet')->first();
        if ($outlet) {
            return (int) $outlet->id_outlet;
        }
        
        // Last resort: return null to prevent foreign key errors
        return null;
    }

    /**
     * Get company settings data for print templates
     */
    protected function getCompanySettingsForPrint()
    {
        $settings = $this->getCompanySettings();
        
        return [
            'company_name' => $settings->company_name,
            'company_code' => $settings->company_code,
            'company_address' => $settings->company_address,
            'company_phone' => $settings->company_phone,
            'company_email' => $settings->company_email,
            'company_website' => $settings->company_website,
            'logo_url' => $settings->logo_url, // This uses the accessor
            'company_logo' => $settings->company_logo, // Raw DB path for DomPDF
            'favicon_url' => $settings->favicon_url,
            'npwp' => $settings->npwp,
            'nib' => $settings->nib,
            'siup' => $settings->siup,
            'tdp' => $settings->tdp,
            'bank_name' => $settings->bank_name,
            'bank_account_number' => $settings->bank_account_number,
            'bank_account_name' => $settings->bank_account_name,
            'currency' => $settings->currency,
            'timezone' => $settings->timezone,
            'date_format' => $settings->date_format,
            'time_format' => $settings->time_format,
            'tax_rate' => $settings->tax_rate,
            'formatted_address' => $settings->formatted_address,
            'formatted_phone' => $settings->formatted_phone,
        ];
    }
}