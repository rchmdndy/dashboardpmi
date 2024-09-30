<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Platform;
use Filament\Http\Middleware\Authenticate;
use App\Providers\Filament\Auth\StaffLogin;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class StaffPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('staff')
            ->path('staff')
            ->login(StaffLogin::class)
            ->colors([
                'primary' => Color::Amber,
                'secondary' => Color::Indigo,
                'blue' => Color::Blue,
                'red' => Color::Red,
                'green' => Color::Green,
                'teal' => Color::Teal,
                'cyan' => Color::Cyan,
                'indigo' => Color::Indigo,
                'Amber' => Color::Amber,
                'yellow' => Color::Yellow,
                'orange' => Color::Orange,
                'purple' => Color::Purple,
                'pink' => Color::Pink,

            ])
            ->databaseNotifications()
            ->userMenuItems([
                MenuItem::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->visible(function (): bool {
                        return true;
                    }),
            ])
            ->plugins([
                SpotlightPlugin::make(),
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('My Profile')
                    ->setNavigationLabel('My Profile')
                    ->setIcon('heroicon-o-user')
                    ->shouldRegisterNavigation(false)
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowBrowserSessionsForm(),
                FilamentApexChartsPlugin::make(),

            ])
            ->sidebarFullyCollapsibleOnDesktop()
            ->globalSearchKeyBindings(['command+k','ctrl+k'])
            ->globalSearchFieldSuffix(fn (): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL+K',
                Platform::Mac => '⌘K',
                default => null,
            })
            ->favicon(asset('images/logo_asli.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([

            ])
            ->discoverPages(in: app_path('Filament/staff/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Staff/Widgets'), for: 'App\\Filament\\Staff\\Widgets')
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
