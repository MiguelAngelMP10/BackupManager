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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('command');
            $table->string('cron_expression');
            $table->enum('status', ['pending', 'in progress', 'completed'])->default('pending')->change();
            $table->timestamp('last_executed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
