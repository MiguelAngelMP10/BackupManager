<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
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
use Saade\FilamentLaravelLog\FilamentLaravelLogPlugin;

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
                    50  => '238, 255, 255',  // Turquesa muy claro, casi blanco
                    100 => '204, 255, 255',  // Turquesa claro
                    200 => '164, 232, 233',  // Turquesa suave
                    300 => '109, 213, 217',  // Turquesa fresco y vibrante
                    400 => '56, 189, 197',    // Turquesa medio
                    500 => '34, 185, 193',    // Turquesa brillante
                    600 => '26, 139, 148',    // Turquesa más profundo
                    700 => '19, 105, 111',     // Turquesa oscuro
                    800 => '12, 71, 76',       // Turquesa intenso
                    900 => '8, 43, 45',        // Turquesa muy oscuro
                    950 => '3, 23, 24',        // Turquesa casi negro
                ],
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
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
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                FilamentLaravelLogPlugin::make()
                    ->navigationGroup('System Tools')
                    ->navigationLabel('Logs')
                    ->navigationIcon('heroicon-o-bug-ant')
                    ->navigationSort(1)
                    ->slug('logs')
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->maxContentWidth(maxContentWidth: MaxWidth::Full)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop();
    }
}
