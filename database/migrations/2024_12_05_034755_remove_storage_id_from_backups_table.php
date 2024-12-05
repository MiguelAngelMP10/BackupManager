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
        Schema::table('backups', function (Blueprint $table) {
            $table->dropForeign(['storage_id']);
            $table->dropColumn('storage_id');
        });

        Schema::table('scheduled_tasks', function (Blueprint $table) {
            $table->dropForeign(['storage_id']);
            $table->dropColumn('storage_id');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->unsignedBigInteger('storage_id')->nullable(); // Ajusta el tipo y las restricciones si es necesario
        });

        Schema::table('scheduled_tasks', function (Blueprint $table) {
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');

        });
    }
};
