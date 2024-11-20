<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Storage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'path',
        'host',
        'username',
        'password',
        'access_key_id',
        'secret_access_key',
        'port',
        'region',
        'bucket',
    ];

    /**
     * Relación con el modelo User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el modelo Backup
     */
    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class);
    }


    /**
     * Relación con el modelo ScheduledTask
     */
    public function scheduledTasks(): HasMany
    {
        return $this->hasMany(ScheduledTask::class);
    }
}
