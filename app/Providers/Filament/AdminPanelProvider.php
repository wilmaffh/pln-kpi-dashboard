<?php
// FILE: app/Providers/Filament/AdminPanelProvider.php
// Menggantikan file yang dibuat saat: php artisan filament:install --panels

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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')

            // ── Branding PLN ──────────────────────────────────────────────
            ->brandName('KPI Dashboard')
            //->brandLogo(fn() => view('filament.brand'))
            ->favicon(asset('favicon.ico'))

            // ── Warna Tema: Merah PLN ─────────────────────────────────────
            ->colors([
                'primary'   => Color::hex('#C0392B'),
                'secondary' => Color::hex('#2C3E50'),
                'success'   => Color::Emerald,
                'warning'   => Color::Amber,
                'danger'    => Color::Rose,
                'info'      => Color::Sky,
            ])

            // ── Font ──────────────────────────────────────────────────────
            ->font('Inter')

            // ── Auth ──────────────────────────────────────────────────────
            ->login()
            ->profile()

            // ── Navigasi ──────────────────────────────────────────────────
            ->navigationGroups([
                'Dashboard',
                'Input Data KPI',
                'Master Data',
                'Laporan',
                'Pengaturan',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')

            // ── Dark Mode ─────────────────────────────────────────────────
            ->darkMode(true)

            // ── Global Search ─────────────────────────────────────────────
            ->globalSearch(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])

            // ── Notifications ─────────────────────────────────────────────
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')

            // ── Pages & Widgets (auto-discover) ───────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([Pages\Dashboard::class])
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\KpiProgressStatsWidget::class,
                \App\Filament\Widgets\SubmissionChartWidget::class,
            ])

            // ── Middleware ────────────────────────────────────────────────
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
            ->authMiddleware([Authenticate::class]);
    }
}