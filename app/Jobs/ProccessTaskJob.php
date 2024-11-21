<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Models\ScheduledTask;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProccessTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ScheduledTask $scheduledTask;

    /**
     * Create a new job instance.
     */
    public function __construct($scheduledTask = [])
    {
        $this->scheduledTask = $scheduledTask;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Lógica de la tarea

        $connection = $this->scheduledTask->connection;
        $storage = $this->scheduledTask->storage;
        $date = date('Y-m-d_H-i-s');

        $backupPath = storage_path('app/Task_' . $this->scheduledTask->name . '_' . $date . '.sql');

        $command = "mysqldump --user={$connection->username} --password={$connection->password} --host={$connection->host} {$connection->database} > {$backupPath}";
        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            Log::info("Respaldo realizado exitosamente en: {$backupPath}");

            $s3Path = 'backups/' . basename($backupPath);


            $config = [
                'driver' => 's3',
                'key' => $storage->access_key_id,
                'secret' => $storage->access_key_secret,
                'region' => $storage->region,
                'bucket' => $storage->bucket,
            ];

            $disk = Storage::build($config);


            if ($disk->put($s3Path, file_get_contents($backupPath))) {
                Log::info("Respaldo subido a S3: {$s3Path}");
            } else {
                Log::info("Respaldo no subido a S3: {$s3Path}");
            }

            $this->scheduledTask->last_executed_at = Carbon::now();
            $this->scheduledTask->save();

            $backup = new Backup();
            $backup->user_id = $connection->user_id;
            $backup->connection_id = $connection->id;
            $backup->storage_id = $storage->id;
            $backup->file_name = $s3Path;

            $backup->save();

            if (file_exists($backupPath)) {
               // unlink($backupPath);
                Log::info("Archivo eliminado correctamente.");
            } else {
                Log::error("El archivo no existe: " . $backupPath);
            }

        } else {
            Log::error("Error al realizar el respaldo.");
        }
        Log::info('Executing ProccessTaskJob => ' . $this->scheduledTask->id);
    }
}
