<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="./art/filalaunch-icon-for-black-bg.png">
    <img src="./art/filalaunch-icon-for-white-bg.png" width="96" height="96" alt="FilaLaunch">
  </picture>
</p>

<h1 align="center">filalaunch</h1>
<p align="center"><strong>Multi-model AI usage tracking + BYOK for FilamentPHP — free, open-source, drops into any Laravel app.</strong></p>

<p align="center">
  <img src="https://img.shields.io/badge/license-MIT-E1672E" alt="MIT License">
  <img src="https://img.shields.io/badge/laravel-12.x-172242" alt="Laravel 12">
  <img src="https://img.shields.io/badge/filament-4.x-172242" alt="FilamentPHP 4">
  <img src="https://img.shields.io/badge/php-8.3%2B-172242" alt="PHP 8.3+">
</p>

---

## What this is

Every AI-powered SaaS eventually needs the same unglamorous thing: a way to track how much AI usage each customer is actually costing you, across more than one model provider, with the option for customers to bring their own API key instead of paying your markup.

Nobody ships that as a clean, drop-in module — so we built one.

**This repo is a free, open-source preview piece of a much bigger project: [FilaLaunch](https://filalaunch.com), a complete no-code SaaS kit built on Laravel & FilamentPHP.** This piece stands entirely on its own — you don't need the rest of FilaLaunch to use it.

## What's inside

| Feature | Status |
|---|---|
| **AI Usage & BYOK Manager** — multi-provider AI gateway (OpenAI, Anthropic, Google, OpenRouter), per-request usage logging, BYOK key management for end customers, provider-agnostic `AIProviderDriver` contract | 🚧 In progress |
| **White-Label Branding (Preset Mode)** — no-code theme editor: logo, colors, font-pair, app name, all editable from the Filament admin panel, zero custom CSS required | 🗺️ Planned |

Foundation already in place: Laravel 12, FilamentPHP 4 admin panel, role/permission management via `spatie/laravel-permission` + `bezhansalleh/filament-shield`, typed settings via `spatie/laravel-settings`, and file/logo uploads via `spatie/laravel-medialibrary`.

## Tech stack

- **Laravel 12** / **PHP 8.3+**
- **FilamentPHP 4** (admin panel)
- **MySQL 8**
- `spatie/laravel-permission` + `bezhansalleh/filament-shield` — dynamic roles & permissions
- `spatie/laravel-settings` — typed application settings (AI provider config, brand settings)
- `spatie/laravel-medialibrary` — logo/asset uploads
- `spatie/laravel-activitylog` — audit trail for key/branding changes

## Installation

```bash
git clone https://github.com/filalaunch/saaskit.git
cd saaskit
composer install
cp .env.example .env
php artisan key:generate
```

Set your database credentials in `.env`, then:

```bash
php artisan migrate
php artisan make:filament-user
```

Visit `/admin` and log in with the user you just created.

## Roadmap

This repo tracks its own small roadmap (the two features above) independently of the full FilaLaunch kit. Watch/star this repo for updates, or head to **[filalaunch.com](https://filalaunch.com)** to get notified when the complete kit — billing, content, support, invoicing, and 18 other modules — ships.

## Contributing

Issues and PRs welcome. If you're extending the `AIProviderDriver` contract to support another provider, keep the interface shape consistent with the existing drivers so it stays a drop-in swap for anyone using this.

## License

MIT — see [LICENSE](./LICENSE). Use it, fork it, ship it in your own project. No attribution required (though a star is always appreciated).

---

<p align="center">Part of <a href="https://filalaunch.com">FilaLaunch</a> — the no-code Laravel SaaS kit.</p>
