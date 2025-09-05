<?php

namespace App\Http\Controllers\Airtable\Sync;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Airtable;
use DB;

use App\Models\Media;

class MediaController extends Controller {
    /**
     * Sync media table - Truncates the
     * table and recreates it with Airtable data
     * 
     * @return void
     */ 
    public function syncMedia () {
        \Log::info("Media table sync started at ".date('Y-m-d H:i:s'));
        error_log("=== MEDIA SYNC STARTED ===");

        $media = Airtable::table('media')->all();
        error_log("AIRTABLE_MEDIA_RAW_RESPONSE: " . json_encode($media));
        error_log("AIRTABLE_MEDIA_TOTAL_RECORDS: " . count($media));
        
        if (count($media) > 0) {
            error_log("AIRTABLE_MEDIA_FIRST_RECORD: " . json_encode($media[0]));
            error_log("AIRTABLE_MEDIA_FIRST_RECORD_FIELDS: " . json_encode(array_keys($media[0]['fields'])));
        }
        if ((Media::count() > 0) && (sizeof($media) > 0)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Media::truncate();
        }

        // Recreate media
        $downloaded = 0;
        $failed = 0;
        
        foreach ($media as $f) {
            $md = new Media;
            $md->airtable_id = @$f["id"];
            
            // Try to get image URL from File field first, then fall back to Link field
            $imageUrl = null;
            if (!empty(@$f["fields"]["File"]) && is_array(@$f["fields"]["File"]) && count(@$f["fields"]["File"]) > 0) {
                // Extract URL from the first file attachment
                $imageUrl = @$f["fields"]["File"][0]["url"];
            } else {
                // Fall back to Link field if File field is empty
                $imageUrl = @$f["fields"]["Link"];
            }
            
            $md->link = $imageUrl;
            $md->type = @$f["fields"]["Type"];
            $md->save();
            
            // Automatically download Airtable images to prevent expiration
            if ($imageUrl && strpos($imageUrl, 'airtableusercontent.com') !== false) {
                if ($md->downloadAndStoreLocally()) {
                    $downloaded++;
                } else {
                    $failed++;
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $count = Media::count();
        \Log::info("Media table sync finished at ".date('Y-m-d H:i:s')." ... ".$count." records synced. Downloaded: {$downloaded} images, Failed: {$failed}");
    }

    /**
     * Simple test method to debug authentication issues
     */
    public function test() {
        return response()->json([
            'message' => 'MediaController test method working!',
            'timestamp' => now()->toISOString(),
            'status' => 'success'
        ]);
    }

    /**
     * Download all expired Airtable images to local storage
     */
    public function downloadExpiredImages() {
        try {
            \Log::info("Downloading expired Airtable images started at ".date('Y-m-d H:i:s'));

            $media = Media::where('is_local', false)
                         ->whereNotNull('link')
                         ->get();

            $downloaded = 0;
            $failed = 0;

            foreach ($media as $item) {
                if ($item->isUrlExpired()) {
                    if ($item->downloadAndStoreLocally()) {
                        $downloaded++;
                    } else {
                        $failed++;
                    }
                }
            }

            \Log::info("Downloading expired images finished. Downloaded: {$downloaded}, Failed: {$failed}");
            
            return response()->json([
                'downloaded' => $downloaded,
                'failed' => $failed,
                'message' => "Downloaded {$downloaded} images, {$failed} failed",
                'timestamp' => now()->toISOString(),
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error("Error in downloadExpiredImages: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'An error occurred while processing the request',
                'timestamp' => now()->toISOString(),
                'status' => 'error'
            ], 500);
        }
    }
}
