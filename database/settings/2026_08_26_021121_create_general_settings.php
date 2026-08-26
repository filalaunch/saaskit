<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Was an empty placeholder migration — now holds the real
        // White-Label Branding (Preset Mode) settings, edited directly
        // per dev-phase instructions rather than adding a second file.
        // Run `php artisan migrate:fresh` for this edit to take effect,
        // since Laravel already recorded the old (empty) version as run.
        $this->migrator->add('brand.app_name', 'My SaaS');
        $this->migrator->add('brand.app_tagline', 'Built with FilaLaunch');
        $this->migrator->add('brand.logo_light', null);
        $this->migrator->add('brand.logo_dark', null);
        $this->migrator->add('brand.favicon', null);
        $this->migrator->add('brand.theme_preset', 'default');
        $this->migrator->add('brand.primary_color', '#E1672E');
        $this->migrator->add('brand.secondary_color', '#172242');
        $this->migrator->add('brand.accent_color', '#E1672E');
        $this->migrator->add('brand.font_pair', 'inter_inter');
        $this->migrator->add('brand.border_radius_scale', 'soft');
        $this->migrator->add('brand.default_color_mode', 'light');
    }
};
