<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'connection_id',
        'file_name',
    ];

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

    public function scheduledTask(): HasMany
    {
        return $this->hasMany(ScheduledTask::class);
    }
}
