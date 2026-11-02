<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Vite;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;

class AdminPanelProvider extends PanelProvider
{

    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('owl-carousel-css', asset('css/owl-carousel/owl.carousel.min.css')),
            Css::make('owl-theme-css', asset('css/owl-carousel/owl.theme.default.min.css')),
        ]);

        FilamentAsset::register([
            Js::make('jquery', asset('js/jquery/jquery.js')),
            Js::make('owl-carousel-js', asset('js/owl-carousel/owl.carousel.min.js')),
            Js::make('custom-main', asset('js/custom/main.js')),
        ]);

        FilamentView::registerRenderHook(
            'BODY_START',
            fn(): string => Blade::render('@livewire(\'livewire-ui-modal\')'),
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'danger' => Color::Rose,
                'gray' => '#F9FAFC',
                'info' => Color::Blue,
                'primary' => '#0094DA',
                'success' => Color::Emerald,
                'warning' => Color::Orange,

            ])

            ->topbar(false)
            ->renderHook(
                'panels::sidebar.nav.start',
                fn(): string => Blade::render('filament.layout.logo'),
            )
            ->renderHook(
                'panels::body.start',
                fn(): string => Blade::render('filament.layout.mobile-page-spacer'),
            )
            ->font('DM Sans')
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->brandName(false)
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
            ])
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/admin/theme.css')

            ->renderHook(
                'panels::script.after',
                fn(): string => Vite::useBuildDirectory('build')
                    ->withEntryPoints(['resources/js/app.js'])->toHtml()
            )
             ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ->plugin(SpatieLaravelTranslatablePlugin::make()->defaultLocales(config('app.supported_locales')),)
        ;
    }
}
