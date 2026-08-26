<?php

namespace App\Filament\Pages;

use App\Services\Branding\ThemeCompiler;
use App\Settings\BrandSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * White-Label Branding — Preset Mode (build spec §4.18). A curated,
 * no-code theme editor: logo, colors, font pair, corner-radius style.
 * Deliberately does NOT expose raw CSS/HTML input anywhere on this page —
 * that's the whole point of Preset Mode being safe by construction.
 * Advanced Mode (custom CSS/fonts, Super-Admin-only) is a separate,
 * later-phase page, not built here.
 */
class BrandingSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static string|\UnitEnum|null $navigationGroup = 'Branding';

    protected static ?string $navigationLabel = 'Branding Settings';

    protected static ?string $title = 'Branding Settings';

    protected string $view = 'filament.pages.branding-settings';

    /**
     * FilaLaunch's own factory defaults (matches the settings migration
     * exactly) — the source of truth for the "Reset to defaults" action.
     *
     * @var array<string, mixed>
     */
    private const DEFAULTS = [
        'app_name' => 'My SaaS',
        'app_tagline' => 'Built with FilaLaunch',
        'logo_light' => null,
        'logo_dark' => null,
        'favicon' => null,
        'primary_color' => '#E1672E',
        'secondary_color' => '#172242',
        'accent_color' => '#E1672E',
        'font_pair' => 'inter_inter',
        'border_radius_scale' => 'soft',
        'default_color_mode' => 'light',
    ];

    /**
     * @var array<string, mixed>
     */
    public array $data = [];

    public function mount(): void
    {
        $settings = app(BrandSettings::class);

        $this->form->fill([
            'app_name' => $settings->app_name,
            'app_tagline' => $settings->app_tagline,
            'logo_light' => $settings->logo_light,
            'logo_dark' => $settings->logo_dark,
            'favicon' => $settings->favicon,
            'primary_color' => $settings->primary_color,
            'secondary_color' => $settings->secondary_color,
            'accent_color' => $settings->accent_color,
            'font_pair' => $settings->font_pair,
            'border_radius_scale' => $settings->border_radius_scale,
            'default_color_mode' => $settings->default_color_mode,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetToDefaults')
                ->label('Reset to defaults')
                ->color('gray')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Reset branding to defaults?')
                ->modalDescription('This resets every field on this page back to FilaLaunch\'s original defaults. Nothing is actually saved until you click "Save changes" afterward — you can still cancel out without losing your current settings.')
                ->modalSubmitActionLabel('Reset fields')
                ->action(function (): void {
                    $this->form->fill(self::DEFAULTS);

                    Notification::make()
                        ->title('Fields reset to defaults')
                        ->body('Nothing has been saved yet — click "Save changes" below to apply this.')
                        ->warning()
                        ->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        TextInput::make('app_name')->required()->maxLength(255),
                        TextInput::make('app_tagline')->maxLength(255),
                        FileUpload::make('logo_light')
                            ->label('Logo (for light backgrounds)')
                            ->image()
                            ->directory('branding')
                            ->disk('public'),
                        FileUpload::make('logo_dark')
                            ->label('Logo (for dark backgrounds)')
                            ->image()
                            ->directory('branding')
                            ->disk('public'),
                        FileUpload::make('favicon')
                            ->image()
                            ->directory('branding')
                            ->disk('public'),
                    ])
                    ->columns(2),
                Section::make('Theme')
                    ->description('Pick colors and a font pair — no CSS required. This compiles into a single theme file used across the whole app.')
                    ->schema([
                        ColorPicker::make('primary_color')->required(),
                        ColorPicker::make('secondary_color')->required(),
                        ColorPicker::make('accent_color')->required(),
                        Select::make('font_pair')
                            ->required()
                            ->options([
                                'inter_inter' => 'Inter / Inter',
                                'poppins_inter' => 'Poppins / Inter',
                                'sora_inter' => 'Sora / Inter',
                                'playfair_source_sans' => 'Playfair Display / Source Sans 3',
                                'space_grotesk_inter' => 'Space Grotesk / Inter',
                                'manrope_inter' => 'Manrope / Inter',
                                'merriweather_karla' => 'Merriweather / Karla',
                                'outfit_inter' => 'Outfit / Inter',
                                'dm_serif_dm_sans' => 'DM Serif Display / DM Sans',
                                'lexend_inter' => 'Lexend / Inter',
                            ]),
                        Select::make('border_radius_scale')
                            ->required()
                            ->options([
                                'sharp' => 'Sharp',
                                'soft' => 'Soft (default)',
                                'round' => 'Round',
                            ]),
                        Select::make('default_color_mode')
                            ->required()
                            ->options([
                                'light' => 'Light',
                                'dark' => 'Dark',
                                'system' => 'Match system',
                            ]),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = app(BrandSettings::class);
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            $settings->{$key} = $value;
        }

        $settings->save();

        app(ThemeCompiler::class)->compile();

        // Filament's own dark-mode JS always prefers a browser's stored
        // localStorage preference over the server-side default — once
        // anyone has ever toggled the theme switch, the saved default
        // becomes invisible until localStorage is cleared. This
        // immediately syncs the new default into the CURRENT browser
        // (the founder's, right now), exactly as if they'd clicked the
        // toggle themselves — no DevTools required to see the change.
        // It deliberately does NOT and can't reach into other visitors'
        // browsers to override a preference they've already chosen for
        // themselves; that's expected, not a limitation of this fix.
        $mode = match ($settings->default_color_mode) {
            'dark' => 'dark',
            'light' => 'light',
            default => 'system',
        };

        // Colors/fonts/radius are baked into the page's <head> at full
        // page-load time via AdminPanelProvider — a Livewire AJAX update
        // alone can't refresh those. A real reload is needed for the
        // founder to instantly see the true effect of their change,
        // rather than only the localStorage-synced theme mode above.
        // Delayed slightly so the "Branding updated" notification is
        // still visible for a moment before the page reloads.
        $this->js(<<<JS
            (function () {
                const mode = '{$mode}';
                localStorage.setItem('theme', mode);
                let applied = mode;
                if (mode === 'system') {
                    applied = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                if (window.Alpine && window.Alpine.store) {
                    window.Alpine.store('theme', applied);
                }
                setTimeout(() => window.location.reload(), 400);
            })();
        JS);

        Notification::make()
            ->title('Branding updated')
            ->success()
            ->send();
    }
}
