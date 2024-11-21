<?php

namespace App\Filament\Pages;

use Filament\Widgets\AccountWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
        ];
    }

}