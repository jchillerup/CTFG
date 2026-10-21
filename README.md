# Civic Tech Field Guide (CTFG)

> The [Connecting Current](https://connectingcurrent.tech) is the world's biggest collection of projects using tech for the common good.

## 🚀 Quick Start

```bash
# Clone and setup
git clone <repository-url> && cd CTFG
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan storage:link
php artisan serve
```

Requires **PHP 8.3+** (8.4 recommended) and **Laravel 13**.

## 📚 Documentation

All documentation lives in the [`documentation/`](documentation/) folder:

- **[Executive Report](documentation/EXECUTIVE_REPORT.md)** - Security audit, secret rotation, and Laravel 13 modernization (Oct 2026)
- **[Setup Guide](documentation/SETUP_GUIDE.md)** - Detailed developer onboarding and setup instructions
- **[Sync Instructions](documentation/SYNC_INSTRUCTIONS.md)** - How the Airtable → SQL sync works (automatic, artisan, and manual)
- **[Complete Project Documentation](documentation/PROJECT_DOCUMENTATION.md)** - Comprehensive guide covering all aspects of the project
- **[Technical Changelog](documentation/TECHNICAL_CHANGELOG.md)** - All improvements and changes made to the project
- **[Laravel Upgrade Plan](documentation/LARAVEL_UPGRADE_PLAN.md)** - The executed Laravel 10 → 13 upgrade plan

## 🎯 Project Overview

The Civic Tech Field Guide is a comprehensive directory of civic technology tools, resources, and organizations. It serves as a centralized hub for discovering and accessing digital tools that support democracy, human rights, and civic engagement.

### Key Features
- **Comprehensive Directory**: Lists of projects, organizations, and resources
- **Advanced Search**: Filter by categories, tags, languages, and more
- **Airtable Integration**: Real-time sync with Airtable database
- **Image Optimization**: Automatic image processing and optimization
- **Responsive Design**: Mobile-first approach with optimized layouts
- **Multi-language Support**: Content in multiple languages

## 🆕 Recent Updates (October 2026)

### Security Hardening (21 Oct 2026)
- ✅ Removed a publicly exposed sync token; `/sync-now/{token}` now uses the `SYNC_SECRET_TOKEN` env secret and fails closed
- ✅ Airtable webhook (`POST /api/webhooks/listing/updated`) requires the `AIRTABLE_WEBHOOK_SECRET` header
- ✅ Token-protected `/api/tp/*` endpoints (`TP_API_TOKEN`); unauthenticated debug/sync routes removed
- ✅ Search SQL fully parameterized; Airtable content escaped; embeds rebuilt through a provider allow-list
- ✅ Production: `APP_DEBUG=false`, secure session cookies, PHP version no longer disclosed

### Platform Modernization (22 Oct 2026)
- ✅ **Laravel 10 → 13** (via 11 and 12) with PHP 8.4 — dependency security audit is clean (was 47 advisories)
- ✅ Replaced the abandoned `tapp/laravel-airtable` package with a first-party REST client (`app/Support/Airtable.php`)
- ✅ Removed unused packages (Livewire, Elasticsearch, fruitcake/laravel-cors — framework CORS is used)
- ✅ Feature smoke tests added (`tests/Feature/SmokeTest.php`) covering homepage, listings, search, and endpoint auth

### Earlier (October 2025)
- ✅ **Image Optimization System**: Comprehensive image processing and thumbnail generation
- ✅ **Mobile Responsiveness**: Optimized layouts for all device sizes
- ✅ **Performance Enhancements**: 80% faster page loads with optimized images

## 🤝 Contributing

The Directory is based on **Laravel 13** with **PHP 8.3+** (8.4 recommended).
If you are not familiar with it, first [check out their docs](https://laravel.com/docs/13.x/) to understand the basic concepts and the requirements to run it locally.

## Get started

The source of truth is an [Airtable database](https://airtable.com/shrfxjImCdCNw9p5U/tblELFP9tGX07UZDo).
When listing projects or filtering them, instead of directly querying Airtable, we use a regularly synced SQL database.
The sync is one-directional; the SQL database is read-only.

### Setting up the site locally

1. Clone the repo (`git clone`) and run `composer install`.
2. Create a `.env` file (`cp .env.example .env`) and fill out the details.
3. Generate a key (`php artisan key:generate`) then run the migrations (`php artisan migrate`) and `php artisan storage:link`.

For a database-light local setup you can use SQLite (see `config/database.php`): set `DB_CONNECTION=sqlite`, `DB_CONNECTION2=sqlite`, and `DB_DATABASE` to the path of a SQLite file (e.g. `database/database.sqlite`), then run `php artisan migrate`.

At this point, the Directory should be functional, although there won't be any project in the database.

#### Importing the database

If you don't yet have access to the Airtable database, you can import the SQL by requesting the database dump in the Slack channel. The dump which will be sent to you privately.

If you have access to the Airtable database and provided the `AIRTABLE_KEY` and `AIRTABLE_BASE` values in the `.env` file, run the `php artisan sync:tables` command. Airtable access uses the built-in client in `app/Support/Airtable.php` (logical table names are mapped in `config/airtable.php`).

To trigger a sync over HTTP (e.g. for non-technical users), set `SYNC_SECRET_TOKEN` in the environment and visit `/sync-now/<token>` — see [Sync Instructions](documentation/SYNC_INSTRUCTIONS.md).

### Running tests

```bash
php artisan test
```

The test suite runs against a throwaway SQLite file (`database/testing.sqlite`, configured in `phpunit.xml`) — it never touches your development database.

## 🚢 Deployment

Production is deployed with [Kamal](https://kamal-deploy.org) (`config/deploy.yml`): the Docker image builds on the production host (remote builder), is pushed to GHCR, and rolls out with zero downtime. Required secrets are referenced (not stored) in `.kamal/secrets` and must be present in the deployer's environment — note in particular that the app key is read from **`LARAVEL_APP_KEY`**. The deployer also needs a local `docker login ghcr.io` (a GitHub token with `write:packages`) so the remote builder can push the image. See the deployment notes in the [Technical Changelog](documentation/TECHNICAL_CHANGELOG.md).
