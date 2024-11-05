<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');          // Nombre descriptivo del almacenamiento
            $table->string('type');          // Tipo (local, s3, ftp, etc.)
            $table->string('path');          // Ruta base para el almacenamiento
            $table->string('host')->nullable();    // Dirección del host (para S3, FTP, etc.)
            $table->string('username')->nullable(); // Nombre de usuario (para S3, FTP, etc.)
            $table->string('password')->nullable(); // Contraseña (para S3, FTP, etc.)
            $table->string('port')->nullable();     // Puerto (por ejemplo, para FTP o conexiones no estándar)
            $table->string('region')->nullable();   // Región (por ejemplo, para Amazon S3)
            $table->string('bucket')->nullable();   // Bucket o contenedor (por ejemplo, para Amazon S3)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storages');
    }
};
