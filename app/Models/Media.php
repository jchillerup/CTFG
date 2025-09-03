<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Media extends Model {
    protected $table = "media";

    protected $fillable = [
        'airtable_id',
        'link',
        'type',
        'local_path',
        'is_local'
    ];

    public function listings() {
        return $this->belongsToMany('App\Models\Listing', 'listing_media', 
          'media_id', 'listing_id');
    }

    /**
     * Get the display URL for the media
     * Returns local path if available, otherwise the original link
     */
    public function getDisplayUrlAttribute()
    {
        if ($this->is_local && $this->local_path) {
            return asset('storage/' . $this->local_path);
        }
        return $this->link;
    }

    /**
     * Check if the Airtable URL is expired
     */
    public function isUrlExpired()
    {
        if (!$this->link || $this->is_local) {
            return false;
        }

        // Check if it's an Airtable URL that might be expired
        if (strpos($this->link, 'airtableusercontent.com') !== false) {
            try {
                $response = Http::head($this->link);
                return $response->status() === 410 || $response->status() === 404;
            } catch (\Exception $e) {
                return true; // Assume expired if we can't reach it
            }
        }

        return false;
    }

    /**
     * Download and store image locally
     */
    public function downloadAndStoreLocally()
    {
        if (!$this->link || $this->is_local) {
            return false;
        }

        try {
            $response = Http::get($this->link);
            if ($response->successful()) {
                $content = $response->body();
                $extension = $this->getImageExtension($this->link);
                $filename = 'media/' . uniqid() . '.' . $extension;
                
                // Store in public storage
                if (Storage::disk('public')->put($filename, $content)) {
                    $this->update([
                        'local_path' => $filename,
                        'is_local' => true
                    ]);
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to download image: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Get image extension from URL or content type
     */
    private function getImageExtension($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        if ($extension && in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return strtolower($extension);
        }
        
        // Default to jpg if no valid extension found
        return 'jpg';
    }
}
