<?php

namespace App\Http\Controllers\Airtable\Sync;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Airtable;
use DB;

use App\Models\Organization;
use App\Models\Listing;

class OrganizationController extends Controller
{
    /**
     * Sync organizations - truncate current database organizations, fill the table
     * with new Airtable organizations
     * 
     * @return void
     */ 
    public function syncOrganizations() {
        \Log::info("Organization table sync started at ".date('Y-m-d H:i:s'));
        
        // First, get all organizations from Airtable
        $allOrganizations = Airtable::table('organizations')->all();
        error_log("ORGANIZATION_SYNC_DEBUG - Total organizations from Airtable: " . count($allOrganizations));
        
        // Get all listings to see which organizations are actually used
        $listings = Airtable::table('listings')->all();
        error_log("ORGANIZATION_SYNC_DEBUG - Total listings from Airtable: " . count($listings));
        
        // Extract unique organization IDs from listings
        $usedOrganizationIds = [];
        foreach ($listings as $listing) {
            if (!empty($listing['fields']['Organization']) && is_array($listing['fields']['Organization'])) {
                foreach ($listing['fields']['Organization'] as $organizationId) {
                    $usedOrganizationIds[] = $organizationId;
                }
            }
        }
        $usedOrganizationIds = array_unique($usedOrganizationIds);
        error_log("ORGANIZATION_SYNC_DEBUG - Unique organizations used in listings: " . count($usedOrganizationIds));
        
        // Filter organizations to only those used in listings
        $organizationsToSync = [];
        foreach ($allOrganizations as $organization) {
            if (in_array($organization['id'], $usedOrganizationIds)) {
                $organizationsToSync[] = $organization;
            }
        }
        error_log("ORGANIZATION_SYNC_DEBUG - Organizations to sync (filtered): " . count($organizationsToSync));
        
        if ((Organization::count() > 0) && (sizeof($organizationsToSync) > 0)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Organization::truncate();
        }

        $processedCount = 0;
        // Recreate only the used organizations
        foreach($organizationsToSync as $org) {
            $processedCount++;
            if ($processedCount % 100 == 0) {
                error_log("ORGANIZATION_SYNC_DEBUG - Processed " . $processedCount . " of " . count($organizationsToSync) . " organizations");
            }
            
            $organization = new Organization;
            $organization->airtable_id = @$org["id"];
            $organization->name = @$org["fields"]["Name"];
            $organization->description = @$org["fields"]["Description"];
            $organization->website_url = @$org["fields"]["Website URL"];
            $organization->type = @$org["fields"]["Type"];
            $organization->status = @$org["fields"]["Status"];
            $organization->save();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $count = Organization::count();
        \Log::info("Organization table sync finished at ".date('Y-m-d H:i:s')." ... ".$count." records synced (filtered from " . count($allOrganizations) . " total).");
    }
}
