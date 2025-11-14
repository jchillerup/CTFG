<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Airtable;

echo "=== Airtable Structure Check ===\n\n";

// Check all configured tables
$tables = config('airtable.tables');

echo "Configured tables:\n";
foreach ($tables as $key => $table) {
    echo "  - {$key} => " . ($table['name'] ?? 'N/A') . "\n";
}

echo "\n=== Checking each table ===\n\n";

foreach ($tables as $key => $table) {
    $tableName = $table['name'] ?? $key;
    echo "--- Table: {$tableName} ({$key}) ---\n";
    
    try {
        $records = Airtable::table($key)->all();
        echo "  Total records: " . count($records) . "\n";
        
        if (count($records) > 0) {
            $firstRecord = $records[0];
            echo "  Fields: " . implode(', ', array_keys($firstRecord['fields'] ?? [])) . "\n";
            
            // Show first record structure
            if ($key === 'listings' && !empty($firstRecord['fields']['Organization'])) {
                echo "  Organization field type: " . gettype($firstRecord['fields']['Organization']) . "\n";
                echo "  Organization field value: " . json_encode($firstRecord['fields']['Organization']) . "\n";
            }
            
            // For organizations table, show sample data
            if ($key === 'organizations' && count($records) > 0) {
                echo "  Sample organization:\n";
                echo "    ID: " . $firstRecord['id'] . "\n";
                echo "    Fields: " . json_encode($firstRecord['fields'], JSON_PRETTY_PRINT) . "\n";
            }
        } else {
            echo "  Table is empty\n";
        }
    } catch (\Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Check specific organization IDs from listings
echo "=== Checking Organization IDs from Listings ===\n\n";
try {
    $listings = Airtable::table('listings')->all();
    $orgIds = [];
    foreach ($listings as $listing) {
        if (!empty($listing['fields']['Organization'])) {
            $orgs = is_array($listing['fields']['Organization']) 
                ? $listing['fields']['Organization'] 
                : [$listing['fields']['Organization']];
            foreach ($orgs as $org) {
                if (is_string($org)) {
                    $orgIds[] = $org;
                }
            }
        }
    }
    $orgIds = array_unique($orgIds);
    echo "Found " . count($orgIds) . " unique organization IDs in listings\n";
    echo "Sample IDs: " . implode(', ', array_slice($orgIds, 0, 5)) . "\n\n";
    
    // Try to fetch a few organizations individually
    echo "Trying to fetch organizations individually:\n";
    foreach (array_slice($orgIds, 0, 3) as $orgId) {
        echo "  Organization ID: {$orgId}\n";
        
        // Try organizations table
        try {
            $org = Airtable::table('organizations')->find($orgId);
            echo "    ✓ Found in organizations table\n";
            echo "    Fields: " . implode(', ', array_keys($org['fields'] ?? [])) . "\n";
            if (!empty($org['fields']['Name'])) {
                echo "    Name: " . $org['fields']['Name'] . "\n";
            }
        } catch (\Exception $e) {
            echo "    ✗ Not in organizations table: " . $e->getMessage() . "\n";
        }
        
        // Try knowledge table
        try {
            $org = Airtable::table('knowledge')->find($orgId);
            echo "    ✓ Found in knowledge table\n";
            echo "    Fields: " . implode(', ', array_keys($org['fields'] ?? [])) . "\n";
            if (!empty($org['fields']['Name'])) {
                echo "    Name: " . $org['fields']['Name'] . "\n";
            }
        } catch (\Exception $e) {
            echo "    ✗ Not in knowledge table: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
} catch (\Exception $e) {
    echo "Error checking listings: " . $e->getMessage() . "\n";
}

echo "\n=== Check Complete ===\n";

