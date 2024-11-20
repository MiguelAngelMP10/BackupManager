<?php

namespace App\Filament\Resources\ScheduledTaskResource\Pages;

use App\Filament\Resources\ScheduledTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScheduledTask extends EditRecord
{
    protected static string $resource = ScheduledTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
