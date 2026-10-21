# Laravel Upgrade Plan — 10 → 13 (CTFG)

> **STATUS: EXECUTED 2026-10-22.** All three steps completed (11.57 → 12.70 → 13.38),
> each gated on the smoke suite + fresh-migrate + full Airtable sync. Two deviations
> from the plan as written: (1) `tapp/laravel-airtable` blocked step 2 and was replaced
> with a first-party REST client (`app/Support/Airtable.php`) keeping the same
> `table()->all()/find()/create()/patch()` API; (2) the gate sync exposed a latent
> `ListingController` bug (`$organization` undefined/stale) and the fact that the
> `Knowledge` table no longer exists in Airtable — both handled in code. Composer audit
> is 0 advisories and the ignore list has been removed. See TECHNICAL_CHANGELOG.md.

Created 2026-10-21 after the security audit. Goal: clear the 4 remaining
`laravel/framework` advisories, which are fixed only in Laravel 12/13
(Laravel 10 is EOL and receives no security fixes). Current state:
Laravel **v10.50.3**, PHP **8.4.27**, Composer audit = 4 advisories in
`laravel/framework` only (explicitly ignored in `composer.json` until this
upgrade lands).

## Why this is smaller than a typical upgrade

Codebase sizing done 2026-10-21:

- **Livewire is installed but unused** — 0 components, 0 `wire:` views.
  Remove the package instead of migrating Livewire 2 → 3.
- **Elasticsearch is used in exactly one place** —
  `app/Console/Commands/IndexListings.php` (via the abandoned
  `cviebrock/laravel-elasticsearch`). Remove the package and either drop the
  command or rewrite it against the official `elastic/elasticsearch-php`
  client / plain HTTP.
- **`fruitcake/laravel-cors` (`dev-develop`)** — CORS is built into Laravel
  since v9 (`config/cors.php` already exists). Remove the package.
- **String-syntax routes** exist only in `routes/api.php` (4 routes) —
  convert to FQCN class references.
- Everything else (Airtable sync, listings, media/GD, markdown, response
  cache) is first-party code on stable APIs.

## Target and stepping

Do it as three small, separately deployable steps, each on its own branch
with the test suite + a manual smoke pass before merging. Do **not** jump
10 → 13 in one composer run: the framework breaking changes are cumulative
and much easier to bisect per major.

| Step | Framework | PHP | Notes |
|---|---|---|---|
| 1 | 10.50 → **11.x** (latest) | ^8.2 (8.4 OK) | Slimming era begins; old app structure still supported |
| 2 | 11.x → **12.x** (≥ 12.60) | ^8.2 (8.4 OK) | Clears 3 of the 4 advisories (CRLF/signed-URL fixes) |
| 3 | 12.x → **13.x** (latest) | ^8.3 (8.4 OK) | Clears the debug-page XSS advisory (< 13.30); current release line |

## Package changes per step

| Package (now) | Action |
|---|---|
| `livewire/livewire ^2.12` | **Remove** (unused) at step 1 |
| `cviebrock/laravel-elasticsearch ^10` | **Remove** (abandoned); drop/rewrite `IndexListings` at step 1 |
| `fruitcake/laravel-cors dev-develop` | **Remove**; use framework CORS at step 1 |
| `laravel/framework ^10.0` | `^11.0` → `^12.0` → `^13.0` |
| `php` constraint `"^7.3|^8.0"` | `"^8.2"` (step 1), `"^8.3"` (step 3) |
| `doctrine/dbal ^3.1` | `^4` from step 1 (only used by historical `->change()` migrations; verify fresh `migrate` on SQLite/MySQL still passes, or drop dbal + squash old migrations) |
| `spatie/laravel-responsecache ^7.2` | Latest major supporting the target framework (check at each step) |
| `spatie/laravel-markdown ^2.2` | Keep latest 2.x (supports newer CommonMark); re-check `html_input`/`allow_unsafe_links` still applied |
| `tapp/laravel-airtable ^2.0` | Verify framework support at each step; if it blocks, pin framework-compatible release or call the Airtable REST API via Guzzle directly (the sync controllers only use `Airtable::table(...)->all()`) |
| `intervention/image ^3.11` | Keep (framework-agnostic) |
| `aws/aws-sdk-php`, `guzzlehttp/guzzle`, `league/html-to-markdown`, `paxha/laravel-recursive-relationships` | Keep latest within constraints; verify `paxha` support at step 2/3 (fallback: replace recursive relations with manual self-joins in `Category`) |
| `spatie/laravel-ignition ^2.0` (dev) | Replace with the ignition version for the target framework |
| `nunomaduro/collision ^7`, `phpunit/phpunit ^10`, `mockery ^1.3` (dev) | Collision `^8`, PHPUnit `^11` (step 1) → `^12` (step 3), Mockery latest |

## Framework breaking changes to apply (cumulative checklist)

From the official 11/12/13 upgrade guides — verify each against this codebase:

- [ ] `bootstrap/app.php` application configuration (11+ skeleton style) —
      migrate exception handling, middleware and routing registration out of
      `app/Http/Kernel.php` / `RouteServiceProvider`, or keep the legacy
      structure only if the target framework still supports it.
- [ ] Convert the 4 string-syntax routes in `routes/api.php` to FQCN.
- [ ] Model `$dates` → `casts()` method / `$casts` audit (13 deprecates `$dates`).
- [ ] Carbon 3 (11+): renamed interval methods, `diffIn*` float returns —
      grep for `diffInMinutes(` etc. (sync controllers use these for logging).
- [ ] Symfony 7 components (11+): request/input API changes, `Mailer` config.
- [ ] Queue/cache/session config key changes per guide (`cache.prefix`, etc.).
- [ ] PHPUnit 11/12: annotation-style tests → attributes (only 2 tests exist).
- [ ] Removed helpers/globals per guide (`Str::`, array helpers are fine;
      check for `currency`/`dispatch_now`-style removals — grep).
- [ ] `doctrine/dbal` 4: column `->change()` comparisons behave differently;
      run a fresh SQLite **and** MySQL `migrate:fresh` in CI for each step.
- [ ] Re-run `composer audit` — after step 2 expect ≤1 advisory, after step 3 expect **0**; then delete the
      `config.policy.advisories.ignore-id` block from `composer.json`.

## Verification gate for every step

```bash
composer update -W            # within the step's constraints
php artisan test              # currently 2 tests — add smoke tests first (below)
php artisan migrate:fresh --database=sqlite   # local
php artisan sync:tables       # against Airtable (staging/local DB)
php artisan serve             # smoke: /, /listing/{slug}, /listings/search?q=, media + thumbnail routes
composer audit                # advisories trending to 0
```

Before step 1, add a handful of feature tests so the gates mean something:
homepage 200, listing page 200, search returns results and the injection
payload returns 200 with no SQL error, webhook 401 without secret,
`/api/tp/docu` 401 without token, `/sync-now/<bad>` 403.

## Risks and rollback

- **Airtable package is the long pole.** If `tapp/laravel-airtable` has no
  release for a target framework, replace it with a thin Guzzle client
  (single `table()->all()` call pattern) rather than stalling the upgrade.
- **Elasticsearch command** may be dead weight — confirm with the team
  whether `IndexListings` is still used before rewriting it.
- Deploy each step with Kamal as usual; rollback is `kamal rollback` /
  previous image tag, since no data migrations are expected (schema is
  unchanged by the framework).

## After the upgrade

- Remove the 4 advisory ignores from `composer.json` and note it in
  `documentation/TECHNICAL_CHANGELOG.md`.
- Set a recurring reminder to track Laravel/PHP release lines (PHP 8.4
  security support and Laravel 13 support windows).
