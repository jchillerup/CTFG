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
        
        // First, get all organizations from Airtable organizations table
        $allOrganizations = Airtable::table('organizations')->all();
        error_log("ORGANIZATION_SYNC_DEBUG - Total organizations from Airtable organizations table: " . count($allOrganizations));
        
        // Also check Knowledge table - organizations might be stored there
        $knowledgeRecords = Airtable::table('knowledge')->all();
        error_log("ORGANIZATION_SYNC_DEBUG - Total records from Airtable Knowledge table: " . count($knowledgeRecords));
        
        // Get all listings to see which organizations are actually used
        $listings = Airtable::table('listings')->all();
        error_log("ORGANIZATION_SYNC_DEBUG - Total listings from Airtable: " . count($listings));
        
        // Extract unique organization IDs from listings and collect organization names
        $usedOrganizationIds = [];
        $organizationNames = []; // Store organization names by ID
        $debugCount = 0;
        foreach ($listings as $listing) {
            if (!empty($listing['fields']['Organization'])) {
                $organizationField = $listing['fields']['Organization'];
                
                // Debug: Log first few Organization fields to see their structure
                if ($debugCount < 3) {
                    error_log("ORGANIZATION_SYNC_DEBUG - Listing Organization field structure: " . json_encode($organizationField));
                    $debugCount++;
                }
                
                // Handle both array and single organization
                if (is_array($organizationField)) {
                    foreach ($organizationField as $orgData) {
                        // Check if it's a linked record with data or just an ID string
                        if (is_array($orgData) && !empty($orgData['id'])) {
                            $orgId = $orgData['id'];
                            $usedOrganizationIds[] = $orgId;
                            // Try to get name from linked record data
                            if (!empty($orgData['name'])) {
                                $organizationNames[$orgId] = $orgData['name'];
                            } elseif (!empty($orgData['fields']['Name'])) {
                                $organizationNames[$orgId] = $orgData['fields']['Name'];
                            }
                        } elseif (is_string($orgData)) {
                            // Just an ID string
                            $usedOrganizationIds[] = $orgData;
                        }
                    }
                } else {
                    // Single organization (not an array)
                    if (is_array($organizationField) && !empty($organizationField['id'])) {
                        $orgId = $organizationField['id'];
                        $usedOrganizationIds[] = $orgId;
                        if (!empty($organizationField['name'])) {
                            $organizationNames[$orgId] = $organizationField['name'];
                        } elseif (!empty($organizationField['fields']['Name'])) {
                            $organizationNames[$orgId] = $organizationField['fields']['Name'];
                        }
                    } elseif (is_string($organizationField)) {
                        $usedOrganizationIds[] = $organizationField;
                    }
                }
            }
        }
        error_log("ORGANIZATION_SYNC_DEBUG - Found " . count($organizationNames) . " organization names from listing data");
        $usedOrganizationIds = array_unique($usedOrganizationIds);
        error_log("ORGANIZATION_SYNC_DEBUG - Unique organizations used in listings: " . count($usedOrganizationIds));
        
        // Check if organization IDs from listings match Knowledge table records
        $knowledgeIds = is_array($knowledgeRecords) ? array_column($knowledgeRecords, 'id') : $knowledgeRecords->pluck('id')->toArray();
        $matchingKnowledgeIds = array_intersect($usedOrganizationIds, $knowledgeIds);
        error_log("ORGANIZATION_SYNC_DEBUG - Organization IDs matching Knowledge table: " . count($matchingKnowledgeIds));
        
        // If organizations are in Knowledge table, use those instead
        if (count($matchingKnowledgeIds) > 0 && count($allOrganizations) == 0) {
            error_log("ORGANIZATION_SYNC_DEBUG - Organizations appear to be in Knowledge table, using Knowledge records");
            // Convert Knowledge records to organization format
            $organizationsToSync = [];
            $knowledgeArray = is_array($knowledgeRecords) ? $knowledgeRecords : $knowledgeRecords->toArray();
            foreach ($knowledgeArray as $knowledge) {
                if (in_array($knowledge['id'], $usedOrganizationIds)) {
                    $organizationsToSync[] = $knowledge;
                }
            }
        } elseif (count($allOrganizations) == 0 && count($usedOrganizationIds) > 0) {
            // If organizations table is empty but we have organization IDs from listings,
            // try to fetch each organization individually from Airtable
            // NOTE: Organization IDs in listings actually point to other LISTINGS, not a separate organizations table!
            error_log("ORGANIZATION_SYNC_DEBUG - Organizations table is empty, fetching organizations individually from Airtable");
            $organizationsToSync = [];
            $foundCount = 0;
            foreach ($usedOrganizationIds as $orgId) {
                try {
                    // Try organizations table first
                    $org = Airtable::table('organizations')->find($orgId);
                    if ($org && !empty($org['fields'])) {
                        $organizationsToSync[] = $org;
                        $foundCount++;
                        error_log("ORGANIZATION_SYNC_DEBUG - Found organization {$orgId} in organizations table");
                    }
                } catch (\Exception $e) {
                    // Try Knowledge table
                    try {
                        $org = Airtable::table('knowledge')->find($orgId);
                        if ($org && !empty($org['fields'])) {
                            $organizationsToSync[] = $org;
                            $foundCount++;
                            error_log("ORGANIZATION_SYNC_DEBUG - Found organization {$orgId} in Knowledge table");
                        }
                    } catch (\Exception $e2) {
                        // Try Listings table - organizations are actually other listings!
                        try {
                            $org = Airtable::table('listings')->find($orgId);
                            if ($org && !empty($org['fields']) && !empty($org['fields']['Project name'])) {
                                // Convert listing to organization format
                                // Keep original fields but add Name field for processing
                                $orgRecord = $org; // Start with original
                                $orgRecord['fields']['Name'] = $org['fields']['Project name']; // Add Name field
                                $organizationsToSync[] = $orgRecord;
                                $foundCount++;
                                error_log("ORGANIZATION_SYNC_DEBUG - Found organization {$orgId} in Listings table (name: " . $org['fields']['Project name'] . ")");
                            } else {
                                error_log("ORGANIZATION_SYNC_DEBUG - Organization {$orgId} found in Listings table but has no Project name");
                            }
                        } catch (\Exception $e3) {
                            error_log("ORGANIZATION_SYNC_DEBUG - Organization {$orgId} not found in organizations, Knowledge, or Listings table: " . $e3->getMessage());
                        }
                    }
                }
            }
            error_log("ORGANIZATION_SYNC_DEBUG - Found {$foundCount} organizations by fetching individually");
        } else {
            // Sync ALL organizations from Airtable organizations table (not just those used in listings)
            // This ensures all organizations are available for filtering and display
            $organizationsToSync = is_array($allOrganizations) ? $allOrganizations : $allOrganizations->toArray();
            
            // IMPORTANT: Also fetch organizations from Listings table for any that are referenced in listings
            // This ensures organizations with placeholder names get updated with real names
            if (count($usedOrganizationIds) > 0) {
                error_log("ORGANIZATION_SYNC_DEBUG - Also checking Listings table for organizations referenced in listings");
                $listingsOrgs = [];
                foreach ($usedOrganizationIds as $orgId) {
                    // Check if this organization already exists in organizationsToSync
                    $exists = false;
                    foreach ($organizationsToSync as $existingOrg) {
                        if (isset($existingOrg['id']) && $existingOrg['id'] === $orgId) {
                            $exists = true;
                            break;
                        }
                    }
                    
                    // If not found, try to fetch from Listings table
                    if (!$exists) {
                        try {
                            $org = Airtable::table('listings')->find($orgId);
                            if ($org && !empty($org['fields']) && !empty($org['fields']['Project name'])) {
                                // Convert listing to organization format
                                $orgRecord = $org;
                                $orgRecord['fields']['Name'] = $org['fields']['Project name'];
                                $listingsOrgs[] = $orgRecord;
                                error_log("ORGANIZATION_SYNC_DEBUG - Found organization {$orgId} in Listings table (name: " . $org['fields']['Project name'] . ") - will update existing placeholder");
                            }
                        } catch (\Exception $e) {
                            // Not found in Listings table, skip
                        }
                    }
                }
                // Merge organizations from Listings table into the sync list
                $organizationsToSync = array_merge($organizationsToSync, $listingsOrgs);
                error_log("ORGANIZATION_SYNC_DEBUG - Added " . count($listingsOrgs) . " organizations from Listings table to sync list");
            }
        }
        error_log("ORGANIZATION_SYNC_DEBUG - Organizations to sync (all): " . count($organizationsToSync));
        error_log("ORGANIZATION_SYNC_DEBUG - Organizations used in listings: " . count($usedOrganizationIds));
        
        // Truncate organizations table to ensure clean state - removes placeholder names and duplicates
        // This ensures only organizations from Airtable/Listings are present
        if (count($organizationsToSync) > 0) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Organization::truncate();
            error_log("ORGANIZATION_SYNC_DEBUG - Truncated organizations table for clean sync");
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $processedCount = 0;
        $skippedCount = 0;
        // Recreate all organizations
        foreach($organizationsToSync as $org) {
            $processedCount++;
            if ($processedCount % 100 == 0) {
                error_log("ORGANIZATION_SYNC_DEBUG - Processed " . $processedCount . " of " . count($organizationsToSync) . " organizations");
            }
            
            // Debug: Log available fields for first few organizations
            if ($processedCount <= 3) {
                error_log("ORGANIZATION_SYNC_DEBUG - Organization {$org['id']} fields: " . json_encode(array_keys($org['fields'] ?? [])));
                error_log("ORGANIZATION_SYNC_DEBUG - Organization {$org['id']} all data: " . json_encode($org));
            }
            
            // Skip organizations without a name (required field)
            // Try different field names since organizations might come from Knowledge table or Listings table
            // If it's from Listings table, it will have "Project name" instead of "Name"
            $orgName = @$org["fields"]["Name"] 
                    ?? @$org["fields"]["Organization Name"] 
                    ?? @$org["fields"]["Organization"] 
                    ?? @$org["fields"]["Project name"]  // For organizations that are actually listings
                    ?? null;
            if (empty($orgName)) {
                $skippedCount++;
                \Log::warning("Skipping organization with airtable_id {$org['id']} - missing Name field. Available fields: " . implode(', ', array_keys($org['fields'] ?? [])));
                error_log("ORGANIZATION_SYNC_DEBUG - Skipping organization {$org['id']} - no Name field. Available fields: " . implode(', ', array_keys($org['fields'] ?? [])));
                continue;
            }
            
            try {
                // Create new organization (table is truncated, so no need to check for existing)
                $organization = new Organization;
                $organization->airtable_id = @$org["id"];
                // Trim whitespace from organization name
                $organization->name = trim($orgName);
                // Generate slug from name (trimmed)
                $organization->slug = Organization::generateSlug($organization->name);
                // Try different field names for description and URL (Knowledge table might use different names)
                $organization->description = @$org["fields"]["Description"] ?? null;
                $organization->website_url = @$org["fields"]["Website URL"] ?? @$org["fields"]["URL"] ?? null;
                $organization->type = @$org["fields"]["Type"] ?? null;
                $organization->status = @$org["fields"]["Status"] ?? null;
                $organization->save();
            } catch (\Exception $e) {
                $skippedCount++;
                \Log::error("Failed to save organization {$org['id']}: " . $e->getMessage());
                error_log("ORGANIZATION_SYNC_DEBUG - Failed to save organization {$org['id']}: " . $e->getMessage());
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create placeholder organizations for any organization IDs referenced in listings
        // that don't exist in the organizations table
        $existingOrgIds = Organization::pluck('airtable_id')->toArray();
        $missingOrgIds = array_diff($usedOrganizationIds, $existingOrgIds);
        
        if (count($missingOrgIds) > 0) {
            \Log::info("Creating placeholder organizations for " . count($missingOrgIds) . " missing organization IDs");
            error_log("ORGANIZATION_SYNC_DEBUG - Creating " . count($missingOrgIds) . " placeholder organizations");
            
            foreach ($missingOrgIds as $orgId) {
                // Check if it already exists (race condition protection)
                $existing = Organization::where('airtable_id', $orgId)->first();
                if (!$existing) {
                    try {
                        // Try to get name from listings data first
                        $orgName = null;
                        if (!empty($organizationNames[$orgId])) {
                            $orgName = $organizationNames[$orgId];
                            \Log::debug("Found organization name from listing data for {$orgId}: {$orgName}");
                        }
                        
                        // If not found, try to fetch from Airtable organizations table
                        if (empty($orgName)) {
                            try {
                                $airtableOrg = Airtable::table('organizations')->find($orgId);
                                if (!empty($airtableOrg['fields']['Name'])) {
                                    $orgName = $airtableOrg['fields']['Name'];
                                    \Log::debug("Found organization name from Airtable organizations table for {$orgId}: {$orgName}");
                                } else {
                                    // Try alternative field names
                                    $altNames = ['Organization Name', 'Organization', 'Title', 'name'];
                                    foreach ($altNames as $altName) {
                                        if (!empty($airtableOrg['fields'][$altName])) {
                                            $orgName = $airtableOrg['fields'][$altName];
                                            \Log::debug("Found organization name from Airtable using field '{$altName}' for {$orgId}: {$orgName}");
                                            break;
                                        }
                                    }
                                    if (empty($orgName)) {
                                        \Log::debug("Organization {$orgId} exists in Airtable organizations table but has no Name field. Available fields: " . implode(', ', array_keys($airtableOrg['fields'] ?? [])));
                                    }
                                }
                            } catch (\Exception $e) {
                                // Organization doesn't exist in Airtable organizations table, try Knowledge table
                                \Log::debug("Organization {$orgId} not found in Airtable organizations table, checking Knowledge table: " . $e->getMessage());
                                try {
                                    $knowledge = Airtable::table('knowledge')->find($orgId);
                                    if (!empty($knowledge['fields']['Name'])) {
                                        $orgName = $knowledge['fields']['Name'];
                                        \Log::debug("Found organization name from Airtable Knowledge table for {$orgId}: {$orgName}");
                                    }
                                } catch (\Exception $e2) {
                                    // Try Listings table - organizations are actually other listings!
                                    \Log::debug("Organization {$orgId} not found in Knowledge table, checking Listings table: " . $e2->getMessage());
                                    try {
                                        $listing = Airtable::table('listings')->find($orgId);
                                        if (!empty($listing['fields']['Project name'])) {
                                            $orgName = $listing['fields']['Project name'];
                                            \Log::debug("Found organization name from Airtable Listings table for {$orgId}: {$orgName}");
                                        }
                                    } catch (\Exception $e3) {
                                        \Log::debug("Organization {$orgId} not found in Listings table either: " . $e3->getMessage());
                                    }
                                }
                            }
                        }
                        
                        // Use placeholder if still no name
                        if (empty($orgName)) {
                            $orgName = "Organization " . substr($orgId, 0, 8);
                            \Log::debug("Using placeholder name for organization {$orgId}: {$orgName}");
                        }
                        
                        $placeholder = new Organization;
                        $placeholder->airtable_id = $orgId;
                        // Trim whitespace from organization name
                        $placeholder->name = trim($orgName);
                        // Generate slug from name (trimmed)
                        $placeholder->slug = Organization::generateSlug($placeholder->name);
                        $placeholder->save();
                        \Log::debug("Created organization for airtable_id: {$orgId} with name: {$orgName}");
                    } catch (\Exception $e) {
                        \Log::error("Failed to create organization {$orgId}: " . $e->getMessage());
                    }
                }
            }
        }

        $count = Organization::count();
        \Log::info("Organization table sync finished at ".date('Y-m-d H:i:s')." ... ".$count." records synced, ".$skippedCount." skipped, ".count($missingOrgIds)." placeholders created (from " . count($allOrganizations) . " total in Airtable).");
    }
}
