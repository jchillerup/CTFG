<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Airtable;

echo "=== Airtable Debug Script ===\n";

// Test 1: Check environment variables
echo "1. Environment Variables:\n";
echo "AIRTABLE_BASE: " . env('AIRTABLE_BASE') . "\n";
echo "AIRTABLE_KEY: " . (env('AIRTABLE_KEY') ? 'SET' : 'NOT SET') . "\n";
echo "AIRTABLE_TABLE: " . (env('AIRTABLE_TABLE') ? env('AIRTABLE_TABLE') : 'NOT SET (using default: Listings)') . "\n";

// Test 2: Try to get all tables
echo "\n2. Testing Airtable connection:\n";
try {
    $listings = Airtable::table('listings')->all();
    echo "Successfully connected to Airtable!\n";
    echo "Number of records retrieved: " . count($listings) . "\n";
    
    if (count($listings) > 0) {
        echo "First record ID: " . $listings[0]['id'] . "\n";
        echo "First record fields: " . json_encode(array_keys($listings[0]['fields'])) . "\n";
    }
} catch (Exception $e) {
    echo "Error connecting to Airtable: " . $e->getMessage() . "\n";
}

// Test 3: Check if table name is correct
echo "\n3. Testing different table names:\n";
$possibleTableNames = ['Listings', 'listings', 'Projects', 'projects', 'Records', 'records'];

foreach ($possibleTableNames as $tableName) {
    try {
        $testRecords = Airtable::table($tableName)->all();
        echo "Table '$tableName': " . count($testRecords) . " records found\n";
    } catch (Exception $e) {
        echo "Table '$tableName': Error - " . $e->getMessage() . "\n";
    }
}

echo "\n=== Debug Complete ===\n";
