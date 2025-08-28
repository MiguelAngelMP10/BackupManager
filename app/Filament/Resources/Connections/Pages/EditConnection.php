<?php

namespace App\Filament\Resources\Connections\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Connections\ConnectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConnection extends EditRecord
{
    protected static string $resource = ConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
