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