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
            // Agregar la columna 'connection_id' después de 'user_id'
            $table->foreignId('connection_id')->after('user_id')->constrained('connections')->onDelete('cascade');

            // Agregar la columna 'storage_id' después de 'connection_id'
            $table->foreignId('storage_id')->after('connection_id')->constrained('storages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            // Eliminar las claves foráneas y las columnas
            $table->dropForeign(['connection_id']);
            $table->dropForeign(['storage_id']);
            $table->dropColumn(['connection_id', 'storage_id']);
        });
    }
};
