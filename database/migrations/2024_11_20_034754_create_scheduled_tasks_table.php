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
        Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('connection_id')->constrained('connections')->onDelete('cascade');
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');
            $table->string('name'); // Nombre de la tarea
            $table->string('cron_expression'); // Expresión cron (* * * * *)
            $table->json('payload')->nullable(); // Datos opcionales para el Job
            $table->boolean('enabled')->default(true); // Activación/desactivación de la tarea
            $table->timestamp('last_executed_at')->nullable(); // Última ejecución
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_tasks');
    }
};
