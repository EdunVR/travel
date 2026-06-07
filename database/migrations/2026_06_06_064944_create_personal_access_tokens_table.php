<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Uses hasTable() guard so re-running on a DB that already has the table
     * (e.g. production restore / backup) does not throw "table already exists".
     */
    public function up(): void
    {
        if (Schema::hasTable('personal_access_tokens')) {
            // Table already exists — skip creation.
            // The companion fix-migration (fix_personal_access_tokens_id_auto_increment)
            // will ensure the id column has AUTO_INCREMENT if it was missing.
            return;
        }

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
