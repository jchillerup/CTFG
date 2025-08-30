<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Airtable;
use App\Models\Listing;

echo "=== Test Exact Sync Save ===\n";

try {
    // Get one record from Airtable
    $listings = Airtable::table('listings')->all();
    $testRecord = $listings[0];
    
    echo "Testing with record: " . $testRecord['id'] . "\n";
    echo "Project name: " . @$testRecord["fields"]["Project name"] . "\n";
    
    // Test the exact save operation from sync
    $list = new Listing;
    $list->airtable_id = @$testRecord["id"];
    $list->name = @$testRecord["fields"]["Project name"];
    $list->slug = \Illuminate\Support\Str::of(@$testRecord["fields"]["Project name"])->slug();
    $list->contact_form_email = @$testRecord["fields"]["Contact form email"];
    $list->introduction = @$testRecord["fields"]["1-liner"];
    $list->type = @$testRecord["fields"]["Type"][0];
    $list->organization_type = @$testRecord["fields"]["Organization type"][0];
    $list->description = @$testRecord["fields"]["Longer description"];
    $list->markdown_description = @$testRecord["fields"]["Longer description"];
    $list->raw_description = @$testRecord["fields"]["deprecated Longer description html"];
    $list->status = @$testRecord["fields"]["Status"];
    $list->wikidata_api_field = @$testRecord["fields"]["Wikidata API Field"];
    $list->pricing_information = @$testRecord["fields"]["Pricing information"];
    $list->no_of_employees = @$testRecord["fields"]["Number of employees"][0];
    $list->used_by = @$testRecord["fields"]["Who's it used by?"];
    $list->features = @$testRecord["fields"]["Features"][0];
    $list->project_stage = @$testRecord["fields"]["Project stage"];
    $list->latitude = @$testRecord["fields"]["Latitude lookup"][0];
    $list->longitude = @$testRecord["fields"]["Longitude lookup"][0];
    $list->website_url = @$testRecord["fields"]["Website URL"];
    $list->twitter_url = @$testRecord["fields"]["Twitter URL"];
    $list->facebook_url = @$testRecord["fields"]["Facebook URL"];
    $list->instagram_url = @$testRecord["fields"]["Instagram URL"];
    $list->youtube_channel = @$testRecord["fields"]["YouTube URL"];
    $list->linkedin_url = @$testRecord["fields"]["LinkedIn URL"];
    $list->contact_page_url = @$testRecord["fields"]["Contact page URL"];
    $list->github_url = @$testRecord["fields"]["Github URL"];
    $list->email = @$testRecord["fields"]["Email"];
    $list->events_page_url = @$testRecord["fields"]["Events page URL"];
    $list->jobs_page_url = @$testRecord["fields"]["Jobs page URL"];
    $list->blog_url = @$testRecord["fields"]["Blog feed URL"];
    $list->tiktok_url = @$testRecord["fields"]["TikTok URL"];
    $list->wikimedia_url = @$testRecord["fields"]["Wikimedia URL"];
    $list->crunchbase_url = @$testRecord["fields"]["Crunchbase URL"];
    $list->slack_url = @$testRecord["fields"]["Slack URL"];
    $list->built_with = @$testRecord["fields"]["Builtwith.com"];
    $list->claimed_status = @$testRecord["fields"]["Claimed status"];
    $list->founded = @$testRecord["fields"]["Founded"];
    $list->closed = @$testRecord["fields"]["Closed"];
    $list->shutdown_reason = @$testRecord["fields"]["If shutdown,what happened?"];
    $list->postmortem = @$testRecord["fields"]["Postmortem"];
    $list->host_organization = @$testRecord["fields"]["Host organization"];
    $list->host_organization_url = @$testRecord["fields"]["Host organization URL"];
    $list->language = @$testRecord["fields"]["Languages(s)"][0];
    $list->secondary_language = @$testRecord["fields"]["Languages(s)"][1];
    $list->open_source = @$testRecord["fields"]["Open source"];
    $list->open_source_license = @$testRecord["fields"]["Open source license"];
    $list->created = @$testRecord["fields"]["Created"];
    $list->last_modified = @$testRecord["fields"]["Last Modified"];
    
    echo "Attempting to save...\n";
    $saveResult = $list->save();
    echo "Save result: " . ($saveResult ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($saveResult) {
        echo "Listing ID: " . $list->id . "\n";
        echo "Verifying save...\n";
        $savedListing = Listing::find($list->id);
        echo "Retrieved listing: " . ($savedListing ? 'FOUND' : 'NOT FOUND') . "\n";
        
        // Clean up
        $savedListing->delete();
        echo "Test record cleaned up\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
    echo "Error file: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
