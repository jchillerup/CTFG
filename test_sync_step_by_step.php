<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Airtable;
use DB;
use App\Models\Listing;

echo "=== Test Sync Step by Step ===\n";

try {
    // Step 1: Get Airtable data
    echo "1. Getting Airtable data...\n";
    $listings = Airtable::table('listings')->all();
    echo "   Airtable records: " . count($listings) . "\n";
    
    // Step 2: Check current database state
    echo "2. Checking database state...\n";
    $currentCount = Listing::count();
    echo "   Current listings in DB: " . $currentCount . "\n";
    
    // Step 3: Truncate if needed
    echo "3. Truncating tables...\n";
    if (count($listings) > 0) {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Listing::truncate();
        DB::table('listing_categories')->truncate();
        DB::table('listing_founders')->truncate();
        DB::table('listing_funding')->truncate();
        DB::table('listing_impact')->truncate();
        DB::table('listing_location')->truncate();
        DB::table('listing_media')->truncate();
        DB::table('listing_tags')->truncate();
        DB::table('listing_links')->truncate();
        echo "   Tables truncated\n";
    }
    
    // Step 4: Process records
    echo "4. Processing records...\n";
    $processedCount = 0;
    $savedCount = 0;
    
    foreach ($listings as $index => $l) {
        echo "   Processing record " . ($index + 1) . "...\n";
        
        // Check Project name condition
        $projectName = @$l["fields"]["Project name"];
        if (!empty($projectName)) {
            echo "     ✓ Project name exists: '$projectName'\n";
            $processedCount++;
            
            // Try to save with minimal fields first
            try {
                $list = new Listing;
                $list->airtable_id = @$l["id"];
                $list->name = @$l["fields"]["Project name"];
                $list->slug = \Illuminate\Support\Str::of(@$l["fields"]["Project name"])->slug();
                $list->introduction = @$l["fields"]["1-liner"];
                
                $saveResult = $list->save();
                if ($saveResult) {
                    echo "     ✓ Saved successfully (ID: " . $list->id . ")\n";
                    $savedCount++;
                } else {
                    echo "     ✗ Save failed\n";
                }
            } catch (Exception $e) {
                echo "     ✗ Save error: " . $e->getMessage() . "\n";
            }
        } else {
            echo "     ✗ Project name empty or missing\n";
        }
        
        // Only process first 3 records for debugging
        if ($index >= 2) {
            echo "     ... (stopping after 3 records for debugging)\n";
            break;
        }
    }
    
    echo "\n5. Summary:\n";
    echo "   Records processed: " . $processedCount . "\n";
    echo "   Records saved: " . $savedCount . "\n";
    echo "   Final DB count: " . Listing::count() . "\n";
    
    // Step 6: Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "6. Foreign key checks re-enabled\n";
    
    // Clean up test records
    if ($savedCount > 0) {
        echo "7. Cleaning up test records...\n";
        $testIds = [];
        for ($i = 0; $i < min(3, count($listings)); $i++) {
            $testIds[] = $listings[$i]['id'];
        }
        Listing::whereIn('airtable_id', $testIds)->delete();
        echo "   Cleanup complete\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
    echo "Error file: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
