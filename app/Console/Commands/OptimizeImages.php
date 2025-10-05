<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize {--generate-thumbnails : Generate thumbnails for all images} {--limit=100 : Limit the number of images to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize existing images and generate thumbnails';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting image optimization...');
        
        $limit = $this->option('limit');
        $generateThumbnails = $this->option('generate-thumbnails');
        
        // Get local images that need optimization
        $media = Media::where('is_local', true)
                     ->whereNotNull('local_path')
                     ->limit($limit)
                     ->get();
        
        $this->info("Found {$media->count()} local images to process");
        
        $progressBar = $this->output->createProgressBar($media->count());
        $progressBar->start();
        
        $optimized = 0;
        $thumbnailsGenerated = 0;
        
        foreach ($media as $item) {
            try {
                // Check if file exists
                if (!Storage::disk('public')->exists($item->local_path)) {
                    $this->warn("File not found: {$item->local_path}");
                    $progressBar->advance();
                    continue;
                }
                
                // Optimize the image
                if ($item->optimizeImage()) {
                    $optimized++;
                }
                
                // Generate thumbnail if requested
                if ($generateThumbnails && $item->generateThumbnail()) {
                    $thumbnailsGenerated++;
                }
                
            } catch (\Exception $e) {
                $this->error("Error processing {$item->local_path}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info("Optimization complete!");
        $this->info("Optimized images: {$optimized}");
        
        if ($generateThumbnails) {
            $this->info("Generated thumbnails: {$thumbnailsGenerated}");
        }
        
        return 0;
    }
}