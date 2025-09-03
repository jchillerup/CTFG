<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Media;

class DownloadExpiredImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:download-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download expired Airtable images to local storage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting download of expired Airtable images...');

        $media = Media::where('is_local', false)
                     ->whereNotNull('link')
                     ->get();

        $total = $media->count();
        $downloaded = 0;
        $failed = 0;
        $bar = $this->output->createProgressBar($total);

        $bar->start();

        foreach ($media as $item) {
            if ($item->isUrlExpired()) {
                if ($item->downloadAndStoreLocally()) {
                    $downloaded++;
                } else {
                    $failed++;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Download completed!");
        $this->info("Total processed: {$total}");
        $this->info("Successfully downloaded: {$downloaded}");
        $this->info("Failed: {$failed}");

        return Command::SUCCESS;
    }
}
