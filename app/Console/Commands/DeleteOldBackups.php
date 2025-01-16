<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteOldBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina registros de la tabla backups con más de 7 días';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $backups = Backup::where('created_at', '<', now()->subDays(7))->get();

        foreach ($backups as $backup) {
            Log::info("Eliminando backup con ID: {$backup->id}", [
                'created_at' => $backup->created_at,
            ]);
            $disk = Storage::disk('s3');
            if ($disk->exists($backup->file_name)) {
                $disk->delete($backup->file_name);
                Log::info('Archivo eliminado correctamente de S3.');
            } else {
                Log::info('El archivo no existe en S3.');
            }

            // Elimina el registro si es necesario
            $backup->delete();
            Log::info("Eliminado backup con ID: {$backup->id}");
        }

    }
}
