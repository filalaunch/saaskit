<?php

namespace App\Services\Branding;

use App\Settings\BrandSettings;
use Illuminate\Support\Facades\File;

/**
 * Recompiles BrandSettings into a small CSS-variables file, rather than
 * computing styles at runtime on every request. Called from
 * BrandingSettings::save() whenever the founder updates their branding.
 */
class ThemeCompiler
{
    /**
     * The 10 confirmed starter font-pairs for Preset Mode (build spec §4.18).
     * Exposed as a public accessor so both this compiler and
     * AdminPanelProvider (which sets the panel's actual UI font) read
     * from the exact same source of truth.
     *
     * @return array<string, array{heading: string, body: string}>
     */
    public static function fontPairs(): array
    {
        return self::$fontPairs;
    }

    /**
     * @var array<string, array{heading: string, body: string}>
     */
    protected static array $fontPairs = [
        'inter_inter' => ['heading' => 'Inter', 'body' => 'Inter'],
        'poppins_inter' => ['heading' => 'Poppins', 'body' => 'Inter'],
        'sora_inter' => ['heading' => 'Sora', 'body' => 'Inter'],
        'playfair_source_sans' => ['heading' => 'Playfair Display', 'body' => 'Source Sans 3'],
        'space_grotesk_inter' => ['heading' => 'Space Grotesk', 'body' => 'Inter'],
        'manrope_inter' => ['heading' => 'Manrope', 'body' => 'Inter'],
        'merriweather_karla' => ['heading' => 'Merriweather', 'body' => 'Karla'],
        'outfit_inter' => ['heading' => 'Outfit', 'body' => 'Inter'],
        'dm_serif_dm_sans' => ['heading' => 'DM Serif Display', 'body' => 'DM Sans'],
        'lexend_inter' => ['heading' => 'Lexend', 'body' => 'Inter'],
    ];

    public function compile(): string
    {
        $settings = app(BrandSettings::class);

        // NOTE: fonts are actually delivered via Filament's built-in
        // BunnyFontProvider (a privacy-respecting Google Fonts proxy),
        // wired in AdminPanelProvider::font() — not self-hosted woff2
        // files as the original spec envisioned. Self-hosting is a
        // reasonable future upgrade (zero third-party requests) but adds
        // real asset-bundling work; using Bunny ships something that
        // actually works today with no extra setup.
        $fontPair = self::$fontPairs[$settings->font_pair] ?? self::$fontPairs['inter_inter'];

        $radius = match ($settings->border_radius_scale) {
            'sharp' => ['sm' => '0.0625rem', 'md' => '0.125rem', 'lg' => '0.1875rem', 'xl' => '0.25rem'],
            'round' => ['sm' => '0.5rem', 'md' => '0.75rem', 'lg' => '1rem', 'xl' => '1.5rem'],
            default => ['sm' => '0.25rem', 'md' => '0.375rem', 'lg' => '0.5rem', 'xl' => '0.75rem'], // 'soft'
        };

        // Filament's own compiled app.css defines --radius-sm/md/lg/xl —
        // that's the real hook into its corner-radius styling (there's no
        // dedicated ->radius() Panel method the way there is for colors/
        // fonts/theme mode). Overriding these directly works because this
        // stylesheet loads via PanelsRenderHook::HEAD_END, which fires
        // after @filamentStyles/app.css in <head>, so it wins the CSS
        // cascade. --fl-* variables are also kept for future use on a
        // public-facing site (landing/blog), which isn't built yet.
        $css = ":root {\n"
            . "  --fl-color-primary: {$settings->primary_color};\n"
            . "  --fl-color-secondary: {$settings->secondary_color};\n"
            . "  --fl-color-accent: {$settings->accent_color};\n"
            . "  --fl-font-heading: '{$fontPair['heading']}', sans-serif;\n"
            . "  --fl-font-body: '{$fontPair['body']}', sans-serif;\n"
            . "  --radius-sm: {$radius['sm']};\n"
            . "  --radius-md: {$radius['md']};\n"
            . "  --radius-lg: {$radius['lg']};\n"
            . "  --radius-xl: {$radius['xl']};\n"
            . "}\n";

        // Fixed filename (not hashed) — makes it trivial for the panel
        // provider / render hook to always reference the same URL. Cache
        // busting is handled separately via url(), which appends the
        // file's modification time as a query string.
        $directory = public_path('branding');
        File::ensureDirectoryExists($directory);
        File::put("{$directory}/theme.css", $css);

        return '/branding/theme.css';
    }

    /**
     * The current compiled theme CSS URL, cache-busted with the file's
     * last-modified time so browsers pick up changes immediately after a
     * save instead of serving a stale cached copy.
     */
    public function url(): ?string
    {
        $path = public_path('branding/theme.css');

        if (! File::exists($path)) {
            return null;
        }

        return '/branding/theme.css?v=' . File::lastModified($path);
    }
}
