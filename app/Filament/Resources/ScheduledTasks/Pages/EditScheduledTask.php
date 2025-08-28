<?php

namespace App\Filament\Resources\ScheduledTasks\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ScheduledTasks\ScheduledTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScheduledTask extends EditRecord
{
    protected static string $resource = ScheduledTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
