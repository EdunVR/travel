<?php

namespace App\Http\Controllers;

use App\Models\PreOrderSetting;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PreOrderSettingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coa_penjualan' => 'required|exists:chart_of_accounts,id',
            'coa_piutang' => 'required|exists:chart_of_accounts,id',
            'coa_uang_muka' => 'required|exists:chart_of_accounts,id',
            'coa_kas_bank' => 'required|exists:chart_of_accounts,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $setting = PreOrderSetting::first();
            
            if ($setting) {
                $setting->update($request->all());
            } else {
                PreOrderSetting::create($request->all());
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan COA berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCoas()
    {
        $coas = ChartOfAccount::orderBy('code')->get();
        $settings = PreOrderSetting::getSettings();

        return response()->json([
            'success' => true,
            'coas' => $coas,
            'settings' => $settings
        ]);
    }
}