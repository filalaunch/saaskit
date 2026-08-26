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
| **AI Usage & BYOK Manager** — multi-provider AI gateway (OpenAI, Anthropic, Google, OpenRouter), per-request usage logging, BYOK key management, provider-agnostic `AIProviderDriver` contract so adding a 5th provider is one new class | ✅ Available |
| **White-Label Branding (Preset Mode)** — no-code theme editor: logo, colors, 10 curated font pairs, corner-radius style, light/dark/system default — all editable from the admin panel, zero custom CSS required, with a one-click "Reset to defaults" | ✅ Available |

Foundation already in place: Laravel 12, FilamentPHP 4 admin panel, role/permission management via `spatie/laravel-permission` + `bezhansalleh/filament-shield`, typed settings via `spatie/laravel-settings`, and file/logo uploads via `spatie/laravel-medialibrary`.

**Coming next:** a customer-facing portal (a second Filament panel) so end customers can self-manage their own BYOK keys and view their own usage, plus proper admin-driven user/role management for teams of more than one admin.

## Tech stack

- **Laravel 12** / **PHP 8.3+**
- **FilamentPHP 4** (admin panel)
- **MySQL 8** (or SQLite, works fine for local dev/testing)
- `spatie/laravel-permission` + `bezhansalleh/filament-shield` — dynamic roles & permissions
- `spatie/laravel-settings` — typed application settings (brand settings, more to come)
- `spatie/laravel-medialibrary` — logo/asset uploads
- `spatie/laravel-activitylog` — audit trail for key/branding changes
- `guzzlehttp/guzzle` — talks directly to each AI provider's REST API (no vendor SDKs), which is what keeps the gateway provider-agnostic

### Requirements

- PHP 8.3 or higher
- Composer 2.x
- MySQL 8 (or SQLite for local testing)
- Laravel 12

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
php artisan db:seed
php artisan storage:link
php artisan shield:generate --all
```

`db:seed` creates a test login (`test@example.com` / `password`) and seeds the AI Usage module with the 4 supported providers plus example models. `storage:link` is required for branding logo/favicon uploads to actually be reachable. `shield:generate --all` wires up roles/permissions for every resource — skip the interactive prompts by confirming the panel is `admin` and answering `no` to "select what to generate."

Visit `/admin`, log in, and you'll find **AI Management** and **Branding** in the sidebar.

## Roadmap

This repo tracks its own small roadmap independently of the full FilaLaunch kit — right now that means a customer-facing portal and multi-admin user/role management. Watch/star this repo for updates, or head to **[filalaunch.com](https://filalaunch.com)** to get notified when the complete kit — billing, content, support, invoicing, and 18 other modules — ships.

## Contributing

Issues and PRs welcome. If you're extending the `AIProviderDriver` contract to support another provider, keep the interface shape consistent with the existing drivers so it stays a drop-in swap for anyone using this. See [CONTRIBUTING.md](./CONTRIBUTING.md) for the full process.

## License

MIT — see [LICENSE](./LICENSE). Use it, fork it, ship it in your own project. No attribution required (though a star is always appreciated).

---

<p align="center">Part of <a href="https://filalaunch.com">FilaLaunch</a> — the no-code Laravel SaaS kit.</p>
