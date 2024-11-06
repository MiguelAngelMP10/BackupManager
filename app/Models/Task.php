<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'command', 'status', 'cron_expression', 'last_executed_at', 'backup_id'
    ];

    // Relación con el modelo Backup
    public function backup(): BelongsTo
    {
        return $this->belongsTo(Backup::class);
    }
}
