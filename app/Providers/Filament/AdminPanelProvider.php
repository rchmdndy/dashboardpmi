<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Providers\Filament\Auth\AdminLogin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentGeneralSettings\FilamentGeneralSettingsPlugin;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(AdminLogin::class)
            ->globalSearch(true)
            ->colors([
                'primary' => Color::hex('#ce0000'),
                'red' => Color::Red,
                'green' => Color::Green,
                'teal' => Color::Teal,
                'cyan' => Color::Cyan,
                'indigo' => Color::Indigo,
                'Amber' => Color::Amber,
                'secondary' => Color::Gray,
                'blue' => Color::Blue,
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
            ->sidebarFullyCollapsibleOnDesktop()
            ->globalSearchKeyBindings(['ctrl+k'])
            ->favicon(asset('images/logo_asli.png'))
            ->plugins([
                SpotlightPlugin::make(),
                FilamentGeneralSettingsPlugin::make()
                    ->setSort(3)
                    ->setIcon('heroicon-o-cog')
                    ->setNavigationGroup('Settings')
                    ->setTitle('General Settings')
                    ->setNavigationLabel('General Settings'),
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('My Profile')
                    ->setNavigationLabel('My Profile')
                    ->setIcon('heroicon-o-user')
                    ->shouldRegisterNavigation(false)
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowBrowserSessionsForm(),

            ])
            // ->brandLogo(asset('images/logodiklat.jpg'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                // Pages\Dashboard::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
