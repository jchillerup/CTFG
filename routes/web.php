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

// Optimized image serving with caching
Route::get('/optimized-image/{mediaId}', function ($mediaId) {
    $media = \App\Models\Media::find($mediaId);
    
    if (!$media || !$media->is_local || !$media->local_path) {
        abort(404);
    }
    
    $path = storage_path('app/public/' . $media->local_path);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path, [
        'Cache-Control' => 'public, max-age=31536000', // 1 year
        'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
        'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT',
    ]);
})->middleware('rate.limit:100,1')->name('optimized.image');

// Optimized thumbnail serving with caching
Route::get('/optimized-thumbnail/{mediaId}', function ($mediaId) {
    $media = \App\Models\Media::find($mediaId);
    
    if (!$media || !$media->is_local || !$media->local_path) {
        abort(404);
    }
    
    $thumbnailPath = $media->getThumbnailPath();
    $path = storage_path('app/public/' . $thumbnailPath);
    
    if (!file_exists($path)) {
        // Generate thumbnail if it doesn't exist
        if ($media->generateThumbnail()) {
            $path = storage_path('app/public/' . $thumbnailPath);
        } else {
            abort(404);
        }
    }
    
    return response()->file($path, [
        'Cache-Control' => 'public, max-age=31536000', // 1 year
        'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
        'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT',
    ]);
})->middleware('rate.limit:100,1')->name('optimized.thumbnail');
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
Route::get('/listing-language/{name}', [\App\Http\Controllers\Category\ProjectController::class, 'getProjectsByLanguage'])->where('name', '.*');

Route::get('/listing-organization/{id}', [\App\Http\Controllers\Category\ProjectController::class, 'getProjectsByOrganization']);
Route::get('/listing-organization-type/{type}', [\App\Http\Controllers\Category\ProjectController::class, 'getProjectsByOrganizationType'])->where('type', '.*');

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
Route::get('/download-expired-images', [\App\Http\Controllers\Airtable\Sync\MediaController::class, 'downloadExpiredImages']);
Route::get('/test-media-controller', [\App\Http\Controllers\Airtable\Sync\MediaController::class, 'test']);

Route::get('/email-templates/contact-form', [\App\Http\Controllers\TestController::class, 'contactFormTemplate']);

// Secret sync endpoint for non-tech users
Route::get('/sync-now/{token}', [\App\Http\Controllers\SecretSyncController::class, 'instantSync'])
    ->where('token', '[a-zA-Z0-9]{32}');

// For kamal
Route::get('/up', function () {
    return response()->noContent(200);
});