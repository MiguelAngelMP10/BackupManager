<?php

namespace App\Filament\Resources\ScheduledTaskResource\Pages;

use App\Filament\Resources\ScheduledTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScheduledTask extends CreateRecord
{
    protected static string $resource = ScheduledTaskResource::class;
}
