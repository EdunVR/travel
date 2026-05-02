<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Outlet;
use App\Traits\HasOutletFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanySettingController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:sistem.settings.view')->only(['index', 'show']);
        $this->middleware('permission:sistem.settings.create')->only(['create', 'store']);
        $this->middleware('permission:sistem.settings.edit')->only(['edit', 'update']);
        $this->middleware('permission:sistem.settings.delete')->only(['destroy']);
    }

    /**
     * Display company settings for current outlet.
     */
    public function index()
    {
        $currentOutletId = $this->getSelectedOutlet();
        
        // Convert to integer if it's not 'ALL'
        if ($currentOutletId === 'ALL' || $currentOutletId === null) {
            // For 'ALL' or null, use the first accessible outlet
            $outlets = $this->getAccessibleOutlets();
            $currentOutletId = $outlets->isNotEmpty() ? $outlets->first()->id_outlet : 1;
        }
        
        $currentOutletId = (int) $currentOutletId;
        $setting = CompanySetting::getOrCreateForOutlet($currentOutletId);
        $outlets = $this->getAccessibleOutlets();
        
        return view('admin.sistem.pengaturan.index', compact('setting', 'outlets', 'currentOutletId'));
    }

    /**
     * Show the form for editing company settings.
     */
    public function edit()
    {
        $currentOutletId = $this->getSelectedOutlet();
        
        // Convert to integer if it's not 'ALL'
        if ($currentOutletId === 'ALL' || $currentOutletId === null) {
            // For 'ALL' or null, use the first accessible outlet
            $outlets = $this->getAccessibleOutlets();
            $currentOutletId = $outlets->isNotEmpty() ? $outlets->first()->id_outlet : 1;
        }
        
        $currentOutletId = (int) $currentOutletId;
        $setting = CompanySetting::getOrCreateForOutlet($currentOutletId);
        $outlets = $this->getAccessibleOutlets();
        
        $currencies = CompanySetting::getCurrencies();
        $timezones = CompanySetting::getTimezones();
        $dateFormats = CompanySetting::getDateFormats();
        $timeFormats = CompanySetting::getTimeFormats();
        
        return view('admin.sistem.pengaturan.edit', compact(
            'setting', 
            'outlets', 
            'currencies', 
            'timezones', 
            'dateFormats', 
            'timeFormats'
        ));
    }

    /**
     * Update company settings.
     */
    public function update(Request $request)
    {
        $currentOutletId = $this->getSelectedOutlet();
        
        // Convert to integer if it's not 'ALL'
        if ($currentOutletId === 'ALL' || $currentOutletId === null) {
            // For 'ALL' or null, use the first accessible outlet
            $outlets = $this->getAccessibleOutlets();
            $currentOutletId = $outlets->isNotEmpty() ? $outlets->first()->id_outlet : 1;
        }
        
        $currentOutletId = (int) $currentOutletId;
        $setting = CompanySetting::getOrCreateForOutlet($currentOutletId);

        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_code' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:1000',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_favicon' => 'nullable|image|mimes:ico,png,jpg,gif,svg|max:1024',
            'npwp' => 'nullable|string|max:50',
            'nib' => 'nullable|string|max:50',
            'siup' => 'nullable|string|max:50',
            'tdp' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'currency' => ['required', Rule::in(array_keys(CompanySetting::getCurrencies()))],
            'timezone' => ['required', Rule::in(array_keys(CompanySetting::getTimezones()))],
            'date_format' => ['required', Rule::in(array_keys(CompanySetting::getDateFormats()))],
            'time_format' => ['required', Rule::in(array_keys(CompanySetting::getTimeFormats()))],
            'tax_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->except(['company_logo', 'company_favicon']);
            $data['is_active'] = $request->boolean('is_active', true);

            // Handle logo upload
            if ($request->hasFile('company_logo')) {
                // Delete old logo
                if ($setting->company_logo && Storage::exists($setting->company_logo)) {
                    Storage::delete($setting->company_logo);
                }

                $logoPath = $request->file('company_logo')->store('company/logos', 'public');
                $data['company_logo'] = $logoPath;
            }

            // Handle favicon upload
            if ($request->hasFile('company_favicon')) {
                // Delete old favicon
                if ($setting->company_favicon && Storage::exists($setting->company_favicon)) {
                    Storage::delete($setting->company_favicon);
                }

                $faviconPath = $request->file('company_favicon')->store('company/favicons', 'public');
                $data['company_favicon'] = $faviconPath;
            }

            $setting->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan perusahaan berhasil diperbarui',
                'data' => $setting->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove logo or favicon.
     */
    public function removeFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:logo,favicon'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe file tidak valid'
            ], 422);
        }

        try {
            $currentOutletId = $this->getSelectedOutlet();
            
            // Convert to integer if it's not 'ALL'
            if ($currentOutletId === 'ALL' || $currentOutletId === null) {
                // For 'ALL' or null, use the first accessible outlet
                $outlets = $this->getAccessibleOutlets();
                $currentOutletId = $outlets->isNotEmpty() ? $outlets->first()->id_outlet : 1;
            }
            
            $currentOutletId = (int) $currentOutletId;
            $setting = CompanySetting::getOrCreateForOutlet($currentOutletId);

            $type = $request->input('type');
            $field = $type === 'logo' ? 'company_logo' : 'company_favicon';

            if ($setting->$field && Storage::exists($setting->$field)) {
                Storage::delete($setting->$field);
            }

            $setting->update([$field => null]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company settings as JSON for API.
     */
    public function getSettings()
    {
        try {
            $currentOutletId = $this->getSelectedOutlet();
            
            // Convert to integer if it's not 'ALL'
            if ($currentOutletId === 'ALL' || $currentOutletId === null) {
                // For 'ALL' or null, use the first accessible outlet
                $outlets = $this->getAccessibleOutlets();
                $currentOutletId = $outlets->isNotEmpty() ? $outlets->first()->id_outlet : 1;
            }
            
            $currentOutletId = (int) $currentOutletId;
            $setting = CompanySetting::getOrCreateForOutlet($currentOutletId);

            return response()->json([
                'success' => true,
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset settings to default.
     */
    public function reset()
    {
        try {
            $currentOutletId = $this->getSelectedOutlet();
            
            // Convert to integer if it's not 'ALL'
            if ($currentOutletId === 'ALL' || $currentOutletId === null) {
                // For 'ALL' or null, use the first accessible outlet
                $outlets = $this->getAccessibleOutlets();
                $currentOutletId = $outlets->isNotEmpty() ? $outlets->first()->id_outlet : 1;
            }
            
            $currentOutletId = (int) $currentOutletId;
            $setting = CompanySetting::getOrCreateForOutlet($currentOutletId);

            // Delete uploaded files
            if ($setting->company_logo && Storage::exists($setting->company_logo)) {
                Storage::delete($setting->company_logo);
            }
            if ($setting->company_favicon && Storage::exists($setting->company_favicon)) {
                Storage::delete($setting->company_favicon);
            }

            // Reset to default values
            $setting->update([
                'company_name' => 'Nama Perusahaan',
                'company_code' => null,
                'company_address' => null,
                'company_phone' => null,
                'company_email' => null,
                'company_website' => null,
                'company_logo' => null,
                'company_favicon' => null,
                'npwp' => null,
                'nib' => null,
                'siup' => null,
                'tdp' => null,
                'bank_name' => null,
                'bank_account_number' => null,
                'bank_account_name' => null,
                'currency' => 'IDR',
                'timezone' => 'Asia/Jakarta',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'tax_rate' => 11.00,
                'is_active' => true,
                'additional_settings' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil direset ke default',
                'data' => $setting->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
