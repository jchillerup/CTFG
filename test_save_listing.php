<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;

echo "=== Test Save Listing ===\n";

try {
    // Test 1: Create a simple listing
    echo "1. Testing simple listing creation...\n";
    $listing = new Listing();
    $listing->airtable_id = 'test-' . time();
    $listing->name = 'Test Project';
    $listing->slug = 'test-project';
    $listing->introduction = 'Test introduction';
    
    $result = $listing->save();
    echo "Save result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Listing ID: " . $listing->id . "\n";
    
    // Test 2: Check if it was actually saved
    $savedListing = Listing::find($listing->id);
    echo "Retrieved listing: " . ($savedListing ? 'FOUND' : 'NOT FOUND') . "\n";
    
    // Test 3: Clean up
    if ($savedListing) {
        $savedListing->delete();
        echo "Test record cleaned up\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
    echo "Error file: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
