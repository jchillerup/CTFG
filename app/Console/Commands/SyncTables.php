<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models;

class SyncTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncates data from tables, recreates them from Airtable';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        error_log('test');

        try {

            // Sync all tables
            error_log('=== STARTING CATEGORIES SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\CategoryController::class)->syncCategories();
            error_log('=== CATEGORIES SYNC COMPLETED ===');

            error_log('=== STARTING FUNDING SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\FundingController::class)->syncFunding();
            error_log('=== FUNDING SYNC COMPLETED ===');

            error_log('=== STARTING IMPACT SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\ImpactController::class)->syncImpact();
            error_log('=== IMPACT SYNC COMPLETED ===');

            error_log('=== STARTING KNOWLEDGE SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\KnowledgeController::class)->syncKnowledge();
            error_log('=== KNOWLEDGE SYNC COMPLETED ===');

            error_log('=== STARTING BOUNDARIES SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\BoundaryController::class)->syncBoundary();
            error_log('=== BOUNDARIES SYNC COMPLETED ===');

            error_log('=== STARTING LOCATIONS SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\LocationController::class)->syncLocation();
            error_log('=== LOCATIONS SYNC COMPLETED ===');

            error_log('=== STARTING MEDIA SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\MediaController::class)->syncMedia();
            error_log('=== MEDIA SYNC COMPLETED ===');

            error_log('=== STARTING PEOPLE SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\PeopleController::class)->syncPeople();
            error_log('=== PEOPLE SYNC COMPLETED ===');

            error_log('=== STARTING TAGS SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\TagController::class)->syncTag();
            error_log('=== TAGS SYNC COMPLETED ===');

            error_log('=== STARTING LINKS SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\LinkController::class)->syncLinks();
            error_log('=== LINKS SYNC COMPLETED ===');

            error_log('=== STARTING LISTINGS SYNC ===');
            app(\App\Http\Controllers\Airtable\Sync\ListingController::class)->syncListing();
            error_log('=== LISTINGS SYNC COMPLETED ===');

        } catch (\Throwable $th) {
            error_log('Error from Airtable auto sync cronjob: ' . $th->getMessage());
        }
    }
}
