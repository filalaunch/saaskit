<?php

namespace App\Providers\Filament;

use App\Settings\BrandSettings;
use App\Services\Branding\ThemeCompiler;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
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
        // Pull the founder's chosen colors from BrandSettings (White-Label
        // Branding, Preset Mode) and generate real Filament color palettes
        // from them via Color::hex(). This is what actually makes the
        // Branding Settings page's color pickers affect the panel — the
        // compiled theme.css file (loaded below) only covers the corner
        // radius, since Filament's own buttons/badges/etc. are driven by
        // this ->colors() palette system, not arbitrary CSS variables.
        //
        // Wrapped defensively: this runs very early in the boot cycle
        // (panel registration), before the database is guaranteed to be
        // ready — e.g. mid-migration on a fresh install, or in some test
        // bootstrapping orders. Falls back to the brand's own default
        // Ember/Ink colors rather than fatal-erroring the whole panel.
        [$primaryColor, $secondaryColor] = $this->resolveBrandColors();
        $bodyFont = $this->resolveBrandFont();
        $themeMode = $this->resolveDefaultThemeMode();

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::hex($primaryColor),
                'gray' => Color::hex($secondaryColor),
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            // Filament only supports a single UI-wide font family (plus
            // separate mono/serif slots) — it has no concept of a distinct
            // "heading font" for its own chrome, unlike a marketing site.
            // We use the font pair's body font here; the pair's heading
            // font matters once a public-facing site (landing/blog) is
            // built and can apply --fl-font-heading directly.
            ->font($bodyFont)
            // Respects the founder's chosen default (light/dark/system)
            // without forcing it — the theme switcher stays available.
            ->defaultThemeMode($themeMode)
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => $this->brandingStylesheetTag(),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Reads primary/secondary colors from BrandSettings, falling back to
     * the brand's own defaults if the settings table isn't ready yet
     * (fresh install pre-migration, certain test bootstrapping orders,
     * etc). Never lets a boot-time DB hiccup take down the whole panel.
     *
     * @return array{0: string, 1: string}
     */
    protected function resolveBrandColors(): array
    {
        try {
            $brand = app(BrandSettings::class);

            return [$brand->primary_color, $brand->secondary_color];
        } catch (\Throwable) {
            return ['#E1672E', '#172242'];
        }
    }

    /**
     * Resolves the font pair's body font (the one Filament's own chrome
     * actually uses — see the comment on ->font() above), falling back to
     * Inter Variable (Filament's own default) if settings aren't ready.
     */
    protected function resolveBrandFont(): string
    {
        try {
            $brand = app(BrandSettings::class);
            $pair = ThemeCompiler::fontPairs()[$brand->font_pair] ?? null;

            return $pair['body'] ?? 'Inter Variable';
        } catch (\Throwable) {
            return 'Inter Variable';
        }
    }

    /**
     * Resolves the founder's chosen default color mode, falling back to
     * System (Filament's own default) if settings aren't ready.
     */
    protected function resolveDefaultThemeMode(): ThemeMode
    {
        try {
            $brand = app(BrandSettings::class);

            return ThemeMode::tryFrom($brand->default_color_mode) ?? ThemeMode::System;
        } catch (\Throwable) {
            return ThemeMode::System;
        }
    }

    /**
     * Renders a <link> tag pointing at the compiled brand theme.css, if it
     * exists yet (it won't on a totally fresh install until Branding
     * Settings has been saved at least once). Cache-busted via the file's
     * modification time so a save takes effect immediately, not after a
     * hard refresh.
     */
    protected function brandingStylesheetTag(): string
    {
        try {
            $url = app(ThemeCompiler::class)->url();
        } catch (\Throwable) {
            return '';
        }

        if (! $url) {
            return '';
        }

        return '<link rel="stylesheet" href="' . e(asset($url)) . '">';
    }
}
