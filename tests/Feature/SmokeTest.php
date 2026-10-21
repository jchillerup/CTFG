<?php

namespace Tests\Feature;

use App\Models\Listing;
use Tests\TestCase;

/**
 * Smoke tests guarding the behaviours the security fixes and the
 * Laravel 10 -> 13 upgrade must not regress. They run against a
 * throwaway SQLite file (see phpunit.xml), never the dev database.
 */
class SmokeTest extends TestCase
{
    public function test_homepage_loads(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_listing_page_loads(): void
    {
        Listing::create([
            'name' => 'Smoke Project',
            'slug' => 'smoke-project',
            'introduction' => 'A project created by the smoke test.',
        ]);

        $this->get('/listing/smoke-project')->assertOk();
    }

    public function test_search_works_and_injection_payload_is_safe(): void
    {
        Listing::create([
            'name' => 'Smoke Project',
            'slug' => 'smoke-project',
            'introduction' => 'A project created by the smoke test.',
        ]);

        $this->get('/listings/search?q=Smoke')->assertOk();

        // The old scopeSearchQuery() interpolated this into orderByRaw();
        // it must now execute as a plain (non-matching) search term.
        $this->get('/listings/search?q=' . urlencode("' OR 1=1 --"))->assertOk();
    }

    public function test_webhook_requires_secret(): void
    {
        config(['services.airtable_webhook.secret' => 'test-secret']);

        $this->postJson('/api/webhooks/listing/updated', ['id' => 'rec123'])
            ->assertUnauthorized();
    }

    public function test_tp_docu_requires_token(): void
    {
        config(['services.tp.api_token' => 'test-token']);

        $this->get('/api/tp/docu?name=whatever')->assertUnauthorized();
    }

    public function test_sync_now_rejects_wrong_token(): void
    {
        config(['app.sync_secret_token' => 'the-real-token']);

        $this->get('/sync-now/' . str_repeat('a', 32))->assertForbidden();
    }
}
