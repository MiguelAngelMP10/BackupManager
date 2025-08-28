<?php

namespace App\Filament\Resources\ScheduledTasks\Pages;

use App\Filament\Resources\ScheduledTasks\ScheduledTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScheduledTask extends CreateRecord
{
    protected static string $resource = ScheduledTaskResource::class;
}
