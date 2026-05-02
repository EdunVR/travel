<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration ini untuk update data affiliator yang sudah ada
     * dengan generate username dari phone_number dan set default values
     */
    public function up()
    {
        // Check if username column exists before querying
        if (!Schema::hasColumn('affiliators', 'username')) {
            return;
        }

        // Update affiliator yang sudah ada tapi belum punya username
        $affiliators = DB::table('affiliators')
            ->whereNull('username')
            ->orWhere('username', '')
            ->get();

        foreach ($affiliators as $aff) {
            // Generate username dari phone number
            $username = 'aff_' . substr($aff->phone_number, -8);
            
            // Pastikan unique
            $counter = 1;
            $originalUsername = $username;
            while (DB::table('affiliators')->where('username', $username)->exists()) {
                $username = $originalUsername . $counter;
                $counter++;
            }

            // Set default password (harus diganti oleh user)
            $defaultPassword = Hash::make('password123');

            // Set default program (HM Seller)
            $defaultProgram = DB::table('partnership_programs')
                ->where('slug', 'hm-seller')
                ->first();

            DB::table('affiliators')
                ->where('id', $aff->id)
                ->update([
                    'username' => $username,
                    'password' => $defaultPassword,
                    'partnership_program_id' => $defaultProgram ? $defaultProgram->id : null,
                    'ppc_commission' => 50,
                    'min_sale_commission' => 500000,
                    'updated_at' => now(),
                ]);
        }

        // Log untuk info
        if ($affiliators->count() > 0) {
            \Log::info("Updated {$affiliators->count()} existing affiliators with username and default values");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Tidak perlu rollback karena ini update data
    }
};
