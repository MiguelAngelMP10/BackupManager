<?php

namespace App\Models;

use Cron\CronExpression;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'cron_expression',
        'payload',
        'enabled',
        'last_executed_at',
        'connection_id',
        'storage_id',
    ];

    /**
     * Verifica si la tarea está lista para ejecutarse según la expresión cron.
     */
    public function isDue(): bool
    {
        $cron = new CronExpression($this->cron_expression);
        return $cron->isDue();
    }

    /**
     * Relación con el modelo User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el modelo Connection
     */
    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }

    /**
     * Relación con el modelo Storage
     */
    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }
}
