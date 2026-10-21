<?php

namespace App\Http\Controllers\Api\Webhooks\Airtable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Jobs\Airtable\UpdateListing;

class ListingController extends Controller {
    public function handleUpdate(Request $request) {
        // Fail closed: without a configured shared secret the webhook is off.
        $secret = (string) config('services.airtable_webhook.secret');
        $provided = (string) ($request->header('X-Airtable-Webhook-Secret') ?? $request->query('secret', ''));

        if ($secret === '' || !hash_equals($secret, $provided)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$request->filled('id')) {
            return response()->json(['message' => 'Missing listing id'], 422);
        }

        UpdateListing::dispatch($request->all());

        return response()->json([
            'message' => 'Received'
        ], 200);
    }
    
}
