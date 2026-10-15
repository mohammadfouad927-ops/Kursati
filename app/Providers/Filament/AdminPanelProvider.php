<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('KURSATI')
            ->brandLogo(fn () => new HtmlString('
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 340 70" class="h-10 w-auto">
              <defs>
                <linearGradient id="kursati-wing-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#0EA5E9" />
                  <stop offset="100%" stop-color="#10B981" />
                </linearGradient>
              </defs>
              <g transform="translate(5, 5)">
                <path d="M 5 0 H 15 V 60 H 5 Z" fill="#1E293B" class="dark:fill-slate-100" />
                <path d="M 15 32 L 42 5 L 50 11 L 23 38 Z" fill="url(#kursati-wing-grad)" />
                <path d="M 23 38 L 50 11 L 46 20 L 19 47 Z" fill="#0EA5E9" opacity="0.8" />
                <path d="M 15 28 L 45 55 L 36 60 L 12 37 Z" fill="url(#kursati-wing-grad)" />
              </g>
              <text x="65" y="32" font-family="sans-serif" font-weight="900" font-size="24" letter-spacing="2" fill="#1E293B" class="dark:fill-white">KURSATI</text>
              <text x="65" y="47" font-family="sans-serif" font-weight="600" font-size="9.5" letter-spacing="1.5" fill="#475569" class="dark:fill-slate-400">LEARNING PLATFORM</text>
              <text x="65" y="62" font-family="sans-serif" font-weight="700" font-size="12" fill="#10B981">كــــورســــاتــــي</text>
            </svg>
            '))
            ->brandLogoHeight('2.8rem')
            ->colors([
                'primary' => Color::Amber,
                'pending'  => Color::hex('#f59e0b'), // Custom Amber/Orange
                'approved' => Color::hex('#10b981'), // Custom Emerald Green
                'failed'   => Color::hex('#ef4444'), // Custom Ruby Red
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
            ]);
    }
}
