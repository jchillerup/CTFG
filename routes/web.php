<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [\App\Http\Controllers\Guest\GuestController::class, 'index']);
Route::get('/all-categories', [\App\Http\Controllers\Guest\GuestController::class, 'index']);
Route::get('/projects/add', [\App\Http\Controllers\Projects\ProjectController::class, 'add']);
Route::get('/listing/{slug}', [\App\Http\Controllers\Projects\ProjectController::class, 'singleProject']);
Route::get('/projects/autocomplete', [\App\Http\Controllers\Projects\ProjectController::class, 'searchAutoComplete'])->name('autocomplete');
Route::get('/listings/search', [\App\Http\Controllers\Projects\ProjectController::class, 'search']);
Route::get('/world-map', [\App\Http\Controllers\Guest\GuestController::class, 'worldMap']);

Route::get('/tech', [\App\Http\Controllers\Category\ParentCategoryController::class, 'theTech']);
Route::get('/people', [\App\Http\Controllers\Category\ParentCategoryController::class, 'thePeople']);
Route::get('/adjacent', [\App\Http\Controllers\Category\ParentCategoryController::class, 'adjacent']);

Route::get('/listing-categories', [\App\Http\Controllers\Category\CategoryHierarchy::class, 'getCategoryHierarchy']);
Route::get('/listing-category/{slug}', [\App\Http\Controllers\Category\ProjectController::class, 'getProjectsByCategory']);

Route::get('/listing-tag/{name}', [\App\Http\Controllers\Category\ProjectController::class, 'getProjectsByTag']);

Route::get('/tags', [\App\Http\Controllers\Category\ProjectController::class, 'tagsTable']);

Route::get('/log-search', [\App\Http\Controllers\Projects\SearchController::class, 'log']);
Route::get('/search-log', [\App\Http\Controllers\Projects\SearchController::class, 'getLog']);

/**
 * Process listing contact form
 * 
 */
Route::post('/listing-contact-form', [\App\Http\Controllers\Projects\ListingContactForm::class, 'processForm']);



Route::get('/about', [\App\Http\Controllers\PagesController::class, 'about']);



Route::get('/sync/manual', [\App\Http\Controllers\Airtable\ImportsController::class, 'manualSync']);
Route::get('/sync/manual/links', [\App\Http\Controllers\Airtable\Sync\LinkController::class, 'syncLinks']);
Route::get('/a/test', [\App\Http\Controllers\Airtable\ImportsController::class, 'test']);
Route::get('/t', [\App\Http\Controllers\TestController::class, 'test']);
Route::get('/fill', [\App\Http\Controllers\TestController::class, 'fillCoverImages']);

Route::get('/email-templates/contact-form', [\App\Http\Controllers\TestController::class, 'contactFormTemplate']);

// For kamal
Route::get('/up', function () {
    return response()->noContent(200);
});

// Temporary migration route
Route::get('/migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return response()->json([
            'status' => 'success',
            'output' => Artisan::output()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Debug database config route
Route::get('/debug-db', function () {
    return response()->json([
        'config' => [
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'password_set' => !empty(config('database.connections.mysql.password')),
        ],
        'env' => [
            'DB_HOST' => env('DB_HOST'),
            'DB_PORT' => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
            'DB_PASSWORD_SET' => !empty(env('DB_PASSWORD')),
        ]
    ]);
});

// Test direct database connection
Route::get('/test-db', function () {
    try {
        $pdo = DB::connection()->getPdo();
        $result = DB::select('SELECT VERSION() as version');
        return response()->json([
            'status' => 'success',
            'connection' => 'OK',
            'version' => $result[0]->version ?? 'unknown'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error', 
            'message' => $e->getMessage()
        ], 500);
    }
});

// Create migrations table manually
Route::get('/create-migrations-table', function () {
    try {
        DB::statement('CREATE TABLE IF NOT EXISTS migrations (
            id int unsigned NOT NULL AUTO_INCREMENT,
            migration varchar(255) NOT NULL,
            batch int NOT NULL,
            PRIMARY KEY (id)
        )');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Migrations table created'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Debug artisan vs web database contexts
Route::get('/debug-artisan', function () {
    try {
        // Test web context
        $webConnection = DB::connection()->getPdo();
        $webConfig = DB::connection()->getConfig();
        
        // Test what artisan sees by running a simple artisan command that touches DB
        ob_start();
        $artisanExitCode = Artisan::call('migrate:status');
        $artisanOutput = Artisan::output();
        ob_end_clean();
        
        return response()->json([
            'web_context' => [
                'connection' => 'OK',
                'config' => $webConfig,
                'pdo_class' => get_class($webConnection)
            ],
            'artisan_context' => [
                'exit_code' => $artisanExitCode,
                'output' => $artisanOutput,
                'app_env' => app()->environment(),
                'config_cached' => app()->configurationIsCached()
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});