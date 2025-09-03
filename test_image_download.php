<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Media;

echo "Testing Media model functionality...\n";

// Test 1: Check if we can access the new fields
$media = Media::first();
if ($media) {
    echo "Found media record with ID: " . $media->id . "\n";
    echo "Original link: " . ($media->link ?? 'null') . "\n";
    echo "Local path: " . ($media->local_path ?? 'null') . "\n";
    echo "Is local: " . ($media->is_local ? 'true' : 'false') . "\n";
    
    // Test the display_url attribute
    echo "Display URL: " . $media->display_url . "\n";
    
    // Test URL expiration check
    if ($media->link) {
        echo "URL expired: " . ($media->isUrlExpired() ? 'true' : 'false') . "\n";
    }
} else {
    echo "No media records found.\n";
}

echo "\nTest completed.\n";
