<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

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
            // Always use relative URL for local storage
            return '/storage/' . $this->local_path;
        }
        return $this->link;
    }

    /**
     * Get optimized thumbnail URL for listings
     * Returns a smaller, optimized version of the image
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->is_local && $this->local_path) {
            $thumbnailPath = $this->getThumbnailPath();
            if (Storage::disk('public')->exists($thumbnailPath)) {
                // Always use relative URL for local storage
                return '/storage/' . $thumbnailPath;
            }
            // Generate thumbnail if it doesn't exist
            if ($this->generateThumbnail()) {
                // Always use relative URL for local storage
                return '/storage/' . $thumbnailPath;
            }
        }
        return $this->display_url;
    }

    /**
     * Get the thumbnail path for this media
     */
    public function getThumbnailPath()
    {
        $pathInfo = pathinfo($this->local_path);
        return $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
    }

    /**
     * Get the appropriate image driver (GD or Imagick)
     */
    private function getImageDriver()
    {
        // Try GD first
        if (extension_loaded('gd')) {
            return new Driver();
        }
        
        // Fallback to Imagick
        if (extension_loaded('imagick')) {
            return new ImagickDriver();
        }
        
        // If neither is available, return null
        return null;
    }

    /**
     * Check if image processing is available
     */
    public function isImageProcessingAvailable()
    {
        return extension_loaded('gd') || extension_loaded('imagick');
    }

    /**
     * Generate a thumbnail for the image
     */
    public function generateThumbnail($width = 250, $height = 180, $quality = 85)
    {
        if (!$this->is_local || !$this->local_path) {
            return false;
        }

        // Check if image processing is available
        if (!$this->isImageProcessingAvailable()) {
            Log::channel('security')->warning('Image processing not available - no GD or Imagick extension', [
                'media_id' => $this->id,
                'local_path' => $this->local_path,
                'timestamp' => now()->toISOString(),
            ]);
            return false;
        }

        try {
            $fullPath = Storage::disk('public')->path($this->local_path);
            if (!file_exists($fullPath)) {
                return false;
            }

            $driver = $this->getImageDriver();
            if (!$driver) {
                Log::channel('security')->error('No image driver available', [
                    'media_id' => $this->id,
                    'local_path' => $this->local_path,
                    'timestamp' => now()->toISOString(),
                ]);
                return false;
            }

            $manager = new ImageManager($driver);
            $image = $manager->read($fullPath);
            
            // Get original dimensions
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $originalRatio = $originalWidth / $originalHeight;
            
            // For very wide images (like logos), use a more aggressive approach
            if ($originalRatio > 4.0) {
                // Extremely wide image (like "European Partnership for Democracy") - very aggressive width limit
                $image->resize(120, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } elseif ($originalRatio > 3.0) {
                // Very wide image (like "Civic Tech Field Guide") - limit width more aggressively
                $image->resize(140, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } elseif ($originalRatio > 2.0) {
                // Wide image (like "accessnow") - moderate width limit
                $image->resize(160, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } elseif ($originalRatio < 0.5) {
                // Very tall image - fit to height
                $image->resize(null, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } else {
                // Normal image - fit within bounds
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $thumbnailPath = $this->getThumbnailPath();
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);
            
            // Ensure thumbnail directory exists
            $thumbnailDir = dirname($thumbnailFullPath);
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Save with optimization
            $image->toJpeg($quality)->save($thumbnailFullPath);
            
            return true;
                } catch (\Exception $e) {
                    Log::channel('security')->error("Failed to generate thumbnail: " . $e->getMessage(), [
                        'media_id' => $this->id,
                        'local_path' => $this->local_path,
                        'error' => $e->getMessage(),
                        'timestamp' => now()->toISOString(),
                    ]);
                    return false;
                }
    }

    /**
     * Generate a mobile-optimized thumbnail (larger for mobile screens)
     */
    public function generateMobileThumbnail($width = 350, $height = 250, $quality = 85)
    {
        if (!$this->is_local || !$this->local_path) {
            return false;
        }

        // Check if image processing is available
        if (!$this->isImageProcessingAvailable()) {
            Log::channel('security')->warning('Image processing not available - no GD or Imagick extension', [
                'media_id' => $this->id,
                'local_path' => $this->local_path,
                'timestamp' => now()->toISOString(),
            ]);
            return false;
        }

        try {
            $fullPath = Storage::disk('public')->path($this->local_path);
            if (!file_exists($fullPath)) {
                return false;
            }

            $driver = $this->getImageDriver();
            if (!$driver) {
                Log::channel('security')->error('No image driver available', [
                    'media_id' => $this->id,
                    'local_path' => $this->local_path,
                    'timestamp' => now()->toISOString(),
                ]);
                return false;
            }

            $manager = new ImageManager($driver);
            $image = $manager->read($fullPath);
            
            // Get original dimensions
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $originalRatio = $originalWidth / $originalHeight;
            
            // For very wide images (like logos), use a more aggressive approach
            if ($originalRatio > 4.0) {
                // Extremely wide image (like "European Partnership for Democracy") - very aggressive width limit
                $image->resize(180, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } elseif ($originalRatio > 3.0) {
                // Very wide image - limit width more aggressively
                $image->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } elseif ($originalRatio > 2.0) {
                // Wide image - moderate width limit
                $image->resize(220, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } elseif ($originalRatio < 0.5) {
                // Very tall image - fit to height
                $image->resize(null, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } else {
                // Normal image - fit within bounds
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $thumbnailPath = $this->getMobileThumbnailPath();
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);
            
            // Ensure thumbnail directory exists
            $thumbnailDir = dirname($thumbnailFullPath);
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Save with optimization
            $image->toJpeg($quality)->save($thumbnailFullPath);
            
            return true;
                } catch (\Exception $e) {
                    Log::channel('security')->error("Failed to generate mobile thumbnail: " . $e->getMessage(), [
                        'media_id' => $this->id,
                        'local_path' => $this->local_path,
                        'error' => $e->getMessage(),
                        'timestamp' => now()->toISOString(),
                    ]);
                    return false;
                }
    }

    /**
     * Get the mobile thumbnail path for this media
     */
    public function getMobileThumbnailPath()
    {
        $pathInfo = pathinfo($this->local_path);
        return $pathInfo['dirname'] . '/mobile-thumbnails/' . $pathInfo['filename'] . '_mobile_thumb.' . $pathInfo['extension'];
    }

    /**
     * Get mobile-optimized thumbnail URL for listings
     */
    public function getMobileThumbnailUrlAttribute()
    {
        if ($this->is_local && $this->local_path) {
            $thumbnailPath = $this->getMobileThumbnailPath();
            if (Storage::disk('public')->exists($thumbnailPath)) {
                // Always use relative URL for local storage
                return '/storage/' . $thumbnailPath;
            }
            // Generate mobile thumbnail if it doesn't exist
            if ($this->generateMobileThumbnail()) {
                // Always use relative URL for local storage
                return '/storage/' . $thumbnailPath;
            }
        }
        return $this->thumbnail_url; // Fallback to regular thumbnail
    }

    /**
     * Optimize the original image for better performance
     */
    public function optimizeImage($quality = 85)
    {
        if (!$this->is_local || !$this->local_path) {
            return false;
        }

        try {
            $fullPath = Storage::disk('public')->path($this->local_path);
            if (!file_exists($fullPath)) {
                return false;
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullPath);
            
            // Get original dimensions
            $width = $image->width();
            $height = $image->height();
            
            // Only optimize if image is larger than 800px in any dimension
            if ($width > 800 || $height > 800) {
                $image->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Save with optimization
            $image->toJpeg($quality)->save($fullPath);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to optimize image: " . $e->getMessage());
            return false;
        }
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
                    
                    // Optimize the downloaded image
                    $this->optimizeImage();
                    
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
