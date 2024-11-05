<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login()
            ->colors([
                'primary' => [
                    50 => '240, 248, 255',  // Azul muy claro, casi blanco (similar a "AliceBlue")
                    100 => '219, 234, 254',  // Azul pálido, suave
                    200 => '191, 219, 254',  // Azul claro y suave
                    300 => '147, 197, 253',  // Azul más brillante y vívido
                    400 => '96, 165, 250',   // Azul cielo, brillante
                    500 => '59, 130, 246',   // Azul medio, equilibrado y vibrante
                    600 => '37, 99, 235',    // Azul más profundo y audaz
                    700 => '29, 78, 216',    // Azul real y profundo
                    800 => '30, 64, 175',    // Azul oscuro y elegante
                    900 => '30, 58, 138',    // Azul noche, muy oscuro
                    950 => '12, 24, 58',     // Azul casi negro, ideal para sombras o fondos oscuros
                ],
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->maxContentWidth(maxContentWidth: MaxWidth::Full);
    }
}
