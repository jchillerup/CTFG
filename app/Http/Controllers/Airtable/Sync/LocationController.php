<?php

namespace App\Http\Controllers\Airtable\Sync;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Airtable;
use DB;

use App\Models\Location;
use App\Models\Country;
use App\Models\Boundary;

class LocationController extends Controller {
    /**
     * Sync locations - truncate current database locations, fill the table
     * with new Airtable locations
     * 
     * @return void
     */ 
    public function syncLocation () {
        \Log::info("Location table sync started at ".date('Y-m-d H:i:s'));
        
        // First, get all locations from Airtable
        $allLocations = Airtable::table('locations')->all();
        error_log("LOCATION_SYNC_DEBUG - Total locations from Airtable: " . count($allLocations));
        
        // Get all listings to see which locations are actually used
        $listings = Airtable::table('listings')->all();
        error_log("LOCATION_SYNC_DEBUG - Total listings from Airtable: " . count($listings));
        
        // Extract unique location IDs from listings
        $usedLocationIds = [];
        foreach ($listings as $listing) {
            if (!empty($listing['fields']['Location']) && is_array($listing['fields']['Location'])) {
                foreach ($listing['fields']['Location'] as $locationId) {
                    $usedLocationIds[] = $locationId;
                }
            }
        }
        $usedLocationIds = array_unique($usedLocationIds);
        error_log("LOCATION_SYNC_DEBUG - Unique locations used in listings: " . count($usedLocationIds));
        
        // Filter locations to only those used in listings
        $locationsToSync = [];
        foreach ($allLocations as $location) {
            if (in_array($location['id'], $usedLocationIds)) {
                $locationsToSync[] = $location;
            }
        }
        error_log("LOCATION_SYNC_DEBUG - Locations to sync (filtered): " . count($locationsToSync));
        
        if ((Location::count() > 0) && (sizeof($locationsToSync) > 0)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Location::truncate();
        }

        $processedCount = 0;
        // Recreate only the used locations
        foreach($locationsToSync as $loc) {
            $processedCount++;
            if ($processedCount % 100 == 0) {
                error_log("LOCATION_SYNC_DEBUG - Processed " . $processedCount . " of " . count($locationsToSync) . " locations");
            }
            
            $boundary = @$loc["fields"]["Boundaries"][0];
            $boundary = Boundary::where('airtable_id', $boundary)->first();
            $country = null;
            if ($boundary) {
                $country = $boundary->name;
            }

            $lc = new Location;
            $lc->airtable_id = @$loc["id"];
            $lc->name = @$loc["fields"]["Name"];
            $lc->country = $country;
            $lc->save();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $count = Location::count();
        \Log::info("Location table sync finished at ".date('Y-m-d H:i:s')." ... ".$count." records synced (filtered from " . count($allLocations) . " total).");
    }

    /**
     * Sync country names
     * 
     */
    public function syncCountryNames() {
        // Turkey
        $alias = "Türkiye";
        $turkey = Location::where('name', 'LIKE', '%'.$alias.'%')->get();
        if ($turkey->count() > 0) {
            foreach ($turkey as $tk) {
                $tk->update([
                    'country' => 'Turkey',
                ]);
            }
        }
    } 
}
