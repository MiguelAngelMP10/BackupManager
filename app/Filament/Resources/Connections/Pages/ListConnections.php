<?php

namespace App\Filament\Resources\Connections\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Connections\ConnectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConnections extends ListRecords
{
    protected static string $resource = ConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
