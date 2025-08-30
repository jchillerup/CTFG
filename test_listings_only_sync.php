<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Airtable;
use DB;
use App\Models\Listing;

echo "=== Test Listings Only Sync ===\n";

try {
    // Get Airtable data
    $listings = Airtable::table('listings')->all();
    echo "Airtable records: " . count($listings) . "\n";
    
    // Truncate tables
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
        echo "Tables truncated\n";
    }
    
    // Process records (simplified version)
    $savedCount = 0;
    foreach ($listings as $l) {
        if (!empty(@$l["fields"]["Project name"])) {
            $list = new Listing;
            $list->airtable_id = @$l["id"];
            $list->name = @$l["fields"]["Project name"];
            $list->slug = \Illuminate\Support\Str::of(@$l["fields"]["Project name"])->slug();
            $list->introduction = @$l["fields"]["1-liner"];
            $list->type = @$l["fields"]["Type"][0];
            $list->organization_type = @$l["fields"]["Organization type"][0];
            $list->description = @$l["fields"]["Longer description"];
            $list->markdown_description = @$l["fields"]["Longer description"];
            $list->raw_description = @$l["fields"]["deprecated Longer description html"];
            $list->status = @$l["fields"]["Status"];
            $list->language = @$l["fields"]["Languages(s)"][0];
            $list->secondary_language = @$l["fields"]["Languages(s)"][1];
            $list->open_source = @$l["fields"]["Open source"];
            $list->open_source_license = @$l["fields"]["Open source license"];
            $list->created = @$l["fields"]["Created"];
            $list->last_modified = @$l["fields"]["Last Modified"];
            
            if ($list->save()) {
                $savedCount++;
            }
        }
    }
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
    echo "Records saved: " . $savedCount . "\n";
    echo "Final DB count: " . Listing::count() . "\n";
    
    // Clean up
    if ($savedCount > 0) {
        Listing::truncate();
        echo "Test records cleaned up\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
    echo "Error file: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
