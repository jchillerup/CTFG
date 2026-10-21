<?php

namespace App\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Minimal Airtable REST client.
 *
 * Replaces the abandoned tapp/laravel-airtable package (capped at
 * Illuminate 11, which blocked the Laravel 12 upgrade). It deliberately
 * keeps the exact call pattern the sync code already uses:
 *
 *     Airtable::table('listings')->all();
 *     Airtable::table('listings')->find($id);
 *     Airtable::table('listings')->patch($id, ['Website URL' => $url]);
 *     Airtable::table('404s')->create(['Url' => $url, ...]);
 *
 * Records are returned as plain decoded Airtable API arrays:
 * ['id' => 'rec…', 'createdTime' => '…', 'fields' => [...]].
 *
 * Registered as the global `Airtable` alias in config/app.php so the
 * existing `use Airtable;` imports keep working untouched.
 */
class Airtable
{
    public static function table(string $table): AirtableTable
    {
        return new AirtableTable($table);
    }
}

class AirtableTable
{
    public function __construct(private string $table)
    {
    }

    /** Fetch every record in the table, following pagination. */
    public function all(): array
    {
        $records = [];
        $offset = null;

        do {
            $query = ['pageSize' => 100];
            if ($offset !== null) {
                $query['offset'] = $offset;
            }

            $response = $this->send('get', $this->url(), $query);

            // Parity with the previous package: a table that no longer
            // exists in the base (e.g. 'Knowledge' was removed from
            // Airtable) reads as empty instead of aborting the whole
            // sync. Genuine auth/permission errors still throw below.
            if ($response->status() === 404
                || ($response->status() === 403
                    && str_contains((string) $response->json('error.type'), 'MODEL_NOT_FOUND'))) {
                return [];
            }

            $data = $this->decode($response);
            $records = array_merge($records, $data['records'] ?? []);
            $offset = $data['offset'] ?? null;
        } while ($offset !== null);

        return $records;
    }

    /** Fetch one record by Airtable record id, or null when missing. */
    public function find(string $id): ?array
    {
        $response = $this->send('get', $this->url('/' . $id));

        if ($response->status() === 404) {
            return null;
        }

        return $this->decode($response);
    }

    /** Create a record from a fields map. */
    public function create(array $fields): array
    {
        return $this->request('post', $this->url(), [], ['fields' => $fields]);
    }

    /** Partially update a record from a fields map. */
    public function patch(string $id, array $fields): array
    {
        return $this->request('patch', $this->url('/' . $id), [], ['fields' => $fields]);
    }

    private function request(string $method, string $url, array $query = [], array $body = []): array
    {
        return $this->decode($this->send($method, $url, $query, $body));
    }

    private function send(string $method, string $url, array $query = [], array $body = []): Response
    {
        $key = (string) config('airtable.key');
        $base = (string) config('airtable.base');

        if ($key === '' || $base === '') {
            throw new RuntimeException('Airtable credentials are not configured (AIRTABLE_KEY / AIRTABLE_BASE).');
        }

        $client = Http::withToken($key)->acceptJson()->timeout(60);

        return $body === []
            ? $client->$method($url, $query)
            : $client->$method($url, array_merge($query, $body));
    }

    private function decode(Response $response): array
    {
        if (! $response->successful()) {
            throw new RuntimeException(
                "Airtable API request failed for table '{$this->table}' (HTTP {$response->status()})."
            );
        }

        return (array) $response->json();
    }

    private function url(string $suffix = ''): string
    {
        // Callers use logical names ('listings', 'knowledge', ...); the
        // actual Airtable table names live in config/airtable.php, as
        // they did for the previous package ('listings' => 'Listings').
        $name = config("airtable.tables.{$this->table}.name", $this->table);

        return 'https://api.airtable.com/v0/'
            . config('airtable.base') . '/'
            . rawurlencode($name) . $suffix;
    }
}
