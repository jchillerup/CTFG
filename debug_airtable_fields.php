<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Airtable;

echo "=== Airtable Fields Debug Script ===\n";

try {
    $listings = Airtable::table('listings')->all();
    echo "Total records retrieved: " . count($listings) . "\n\n";
    
    $processedCount = 0;
    $skippedCount = 0;
    
    foreach ($listings as $index => $record) {
        echo "Record " . ($index + 1) . " (ID: " . $record['id'] . "):\n";
        
        // Check if Project name field exists and has value
        $projectName = @$record["fields"]["Project name"];
        echo "  - Project name field exists: " . (isset($record["fields"]["Project name"]) ? 'YES' : 'NO') . "\n";
        echo "  - Project name value: " . ($projectName ?: 'EMPTY/NULL') . "\n";
        echo "  - Project name length: " . strlen($projectName) . "\n";
        echo "  - !empty() check: " . (!empty($projectName) ? 'TRUE' : 'FALSE') . "\n";
        
        if (!empty($projectName)) {
            echo "  ✓ Would be processed\n";
            $processedCount++;
        } else {
            echo "  ✗ Would be skipped\n";
            $skippedCount++;
        }
        
        // Show first few fields for reference
        if ($index < 3) {
            echo "  - Available fields: " . implode(', ', array_keys($record['fields'])) . "\n";
        }
        
        echo "\n";
        
        // Only show first 5 records to avoid spam
        if ($index >= 4) {
            echo "... (showing first 5 records only)\n";
            break;
        }
    }
    
    echo "=== Summary ===\n";
    echo "Total records: " . count($listings) . "\n";
    echo "Would be processed: " . $processedCount . "\n";
    echo "Would be skipped: " . $skippedCount . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
