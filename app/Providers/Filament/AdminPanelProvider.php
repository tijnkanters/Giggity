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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Filament\View\PanelsRenderHook;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->brandName('Giggity')
            ->brandLogo(new HtmlString('<style>.giggity-brand{font-weight:800;letter-spacing:-0.025em;color:#f59e0b;font-size:1.25rem;display:flex;align-items:center;white-space:nowrap}.giggity-brand-text{display:none}.fi-sidebar-open .giggity-brand-text{display:inline}.fi-simple-layout .giggity-brand{font-size:2.5rem}.fi-simple-layout .giggity-brand-text{display:inline}</style><span class="giggity-brand"><span>G</span><span class="giggity-brand-text">iggity</span></span>'))
            ->darkModeBrandLogo(new HtmlString('<span class="giggity-brand"><span>G</span><span class="giggity-brand-text">iggity</span></span>'))
            ->favicon(asset('favicon.svg'))
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
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
                \App\Http\Middleware\EnsureUserBelongsToOrganization::class,
            ])
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_START,
                fn() => new HtmlString(
                    '<style>.org-label{display:none}.fi-sidebar-open .org-label{display:flex}</style>
                    <script>if(window.innerWidth<1024){document.body.classList.remove("fi-sidebar-open")}</script>
                    <div style="margin-bottom: 0.25rem;">
                        <span class="org-label" style="font-size: 0.8rem; font-weight: 600; text-transform: capitalize; letter-spacing: 0.05em; color: rgb(156 163 175); align-items: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            ' . e(Auth::user()?->currentOrganization?->name ?? '') . '
                        </span>
                    </div>'
                ),
            );
    }
}
