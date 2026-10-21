# Security & Platform Modernization — Executive Report

**Project:** Civic Tech Field Guide / Connecting Current (directory.connectingcurrent.tech)
**Period:** 21–22 October 2026
**Status:** Completed and verified in production

## Executive summary

A full security audit of the directory platform found 11 vulnerabilities, including three critical issues that were exploitable by anyone on the internet: a site-wide sync trigger whose token was published in our public code repository, an unauthenticated endpoint that could overwrite any listing, and an endpoint that handed out stored Microsoft access credentials. All findings have been fixed, the affected secrets have been rotated, and every fix has been verified live in production.

In the same engagement, the platform was modernized from Laravel 10 (end-of-life, no longer receiving security patches) to Laravel 13 and PHP 8.4.27. Known dependency vulnerabilities went from **47 to zero**. Both production deployments were zero-downtime, and the previous release remains available for instant rollback.

| Metric | Before | After |
|---|---|---|
| Known dependency vulnerabilities | 47 | **0** |
| Audit findings open | 11 (3 critical, 4 high) | **0** |
| Framework | Laravel 10.49 (end-of-life) | Laravel 13.38 (supported) |
| Runtime | PHP 8.4.15 | PHP 8.4.27 (security release) |
| Production downtime | — | 0 minutes across 2 deploys |

## Risk exposure — before and after

| Finding | What it meant in practice | Status |
|---|---|---|
| Sync token published publicly (Critical) | Anyone could force a full site re-sync at will — denial-of-service and data-integrity risk. Token was in a public GitHub repo and in our documentation. | Fixed & rotated — old token verified rejected |
| Unauthenticated listing overwrite (Critical) | Anyone could silently change the name, description, or status of any listing via the webhook endpoint. | Fixed — secret required; verified live |
| Stored Microsoft credentials exposed (Critical) | An open API endpoint returned stored Microsoft access/refresh tokens and allowed overwriting them. | Endpoint locked — tokens must still be revoked by their owner (below) |
| Open destructive/debug endpoints (High) | Unauthenticated URLs could trigger syncs, mass updates, and test emails. | Removed — all return 404 (verified) |
| SQL injection pattern in search (High) | Search input reached raw SQL; error-based injection demonstrated locally. | Fixed — fully parameterized; automated test coverage |
| Stored XSS surface (Medium) | Airtable-edited content rendered unescaped; a compromised Airtable account could inject scripts into visitors' browsers. | Fixed — escaped output; provider allow-list for embeds |
| Debug mode in deploy config (High) | Production config invited debug pages that can leak environment secrets. | Fixed — debug off; secure session cookies |
| Outdated platform (High/Medium) | PHP missed a year of security fixes; Laravel 10 EOL with 4 unpatchable advisories. | Fixed — PHP 8.4.27, Laravel 13.38, audit clean |

## What was delivered

1. **Audit** — code, route, dependency, and configuration review plus live probes of production. 11 findings documented with evidence and severity.
2. **Hardening** — endpoint authentication, token handling, SQL parameterization, output escaping, embed allow-listing, deploy-config fixes; guarded by 6 new automated smoke tests.
3. **Rotation & redeploy** — new sync token, webhook secret, and API token; zero-downtime Kamal deployment (PHP 8.4.27, debug off, secure cookies). Old public token verified dead; previous release retained for rollback.
4. **Framework modernization** — Laravel 10 → 11 → 12 → 13 in separately gated steps; an abandoned Airtable library replaced with a small first-party client; unused packages removed (Livewire, Elasticsearch, CORS). A full Airtable sync on the new stack reproduces identical data (294 listings, 524 impact records, 206 media, 76 organizations, 70 tags).

Two previously unknown issues surfaced during the upgrade and were fixed: the Airtable "Knowledge" table had been deleted upstream and was failing silently for an unknown period (now handled explicitly), and a latent crash in the listings sync — masked for years by data ordering — was corrected.

## Production verification (22 Oct 2026)

- Site, listing, category, tag, and search pages: **200**
- Removed debug/sync endpoints: **404**
- Webhook without secret; token API without token: **401**
- Old public sync token: **403** (rotation proven)
- PHP version disclosure header: **absent**; session cookie: **Secure + HttpOnly**
- Web, scheduler, database containers: healthy on Laravel 13.38 / PHP 8.4.27

## Content health

A crawl of 356 pages and 149 outbound links found no broken internal pages or images. Eight listings point to external sites that are down or gone (`digitalresilience.network`, `criticalinfralab.net`, `mujeresdelsur.org`, `civicspace.tech`, `icnl.org`). Listing data flows from Airtable, so these need correction in Airtable (update the URL or mark the listing inactive), not in code.

## Remaining actions

| Action | Owner | Priority |
|---|---|---|
| Revoke and re-issue the stored Microsoft access/refresh tokens exposed while the endpoint was open | Integration owner (Microsoft/DocuSign admin) | **High** — the only exposure not fully closed by this engagement |
| Share the new manual-sync link with its non-tech users (old link dead by design) | Operations | Medium |
| Give the new webhook secret to whoever maintains the Airtable automations | Operations | Medium |
| Correct the 8 dead outbound listings in Airtable | Content / data owner | Medium |
| Delete the accidental `ryanmunene79/CTFG` fork created during deploy setup | Account owner | Low |
| Track Laravel/PHP release lines quarterly so the platform never sits on an end-of-life version again | Engineering | Medium |

## Artifacts

- Code and full history: branch `security-hardening-2026-10` (github.com/jchillerup/CTFG)
- `documentation/TECHNICAL_CHANGELOG.md` — sections of 21–22 Oct 2026
- `documentation/LARAVEL_UPGRADE_PLAN.md` — marked executed, with deviations noted
- No production data was lost at any point; database contents were untouched by the upgrade.
