<!DOCTYPE html>
<html>
<head>
    <title>Image Debug</title>
</head>
<body>
    <h1>Image Debug Test</h1>
    
    @php
        $media = App\Models\Media::first();
    @endphp
    
    @if($media)
        <h2>Media ID: {{ $media->id }}</h2>
        <p><strong>Display URL:</strong> {{ $media->display_url }}</p>
        <p><strong>Thumbnail URL:</strong> {{ $media->thumbnail_url }}</p>
        <p><strong>Is Local:</strong> {{ $media->is_local ? 'Yes' : 'No' }}</p>
        <p><strong>Local Path:</strong> {{ $media->local_path }}</p>
        
        <h3>Original Image:</h3>
        <img src="{{ $media->display_url }}" alt="Original" style="max-width: 200px; border: 1px solid #ccc;">
        
        <h3>Thumbnail Image:</h3>
        <img src="{{ $media->thumbnail_url }}" alt="Thumbnail" style="max-width: 200px; border: 1px solid #ccc;">
        
        <h3>File Sizes:</h3>
        @php
            $originalPath = storage_path('app/public/' . $media->local_path);
            $thumbnailPath = storage_path('app/public/' . $media->getThumbnailPath());
        @endphp
        <p><strong>Original:</strong> {{ file_exists($originalPath) ? number_format(filesize($originalPath) / 1024, 1) . ' KB' : 'Not found' }}</p>
        <p><strong>Thumbnail:</strong> {{ file_exists($thumbnailPath) ? number_format(filesize($thumbnailPath) / 1024, 1) . ' KB' : 'Not found' }}</p>
    @else
        <p>No media found</p>
    @endif
</body>
</html>



