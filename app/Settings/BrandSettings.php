<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * White-Label Branding — Preset Mode (per the FilaLaunch build spec §4.18).
 * A single, app-wide settings singleton covering everything a founder can
 * change about their app's identity without touching code: logo, colors,
 * font pair, and default color mode. Advanced Mode (custom CSS, custom
 * font upload) is a deliberately separate, later-phase feature — not
 * included here.
 */
class BrandSettings extends Settings
{
    public string $app_name;

    public ?string $app_tagline;

    public ?string $logo_light;

    public ?string $logo_dark;

    public ?string $favicon;

    public string $theme_preset;

    public string $primary_color;

    public string $secondary_color;

    public string $accent_color;

    public string $font_pair;

    public string $border_radius_scale;

    public string $default_color_mode;

    public static function group(): string
    {
        return 'brand';
    }
}
