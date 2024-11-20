<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('storages', function (Blueprint $table) {
            $table->string('access_key_id')->nullable()->after('password');
            $table->string('secret_access_key')->nullable()->after('access_key_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storages', function (Blueprint $table) {
            $table->dropColumn(['access_key_id', 'secret_access_key']);
        });
    }
};
