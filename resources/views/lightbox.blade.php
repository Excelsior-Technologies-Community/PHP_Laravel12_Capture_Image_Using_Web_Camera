<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightbox - Image Viewer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <style>
        body { background: #000; margin: 0; overflow: hidden; }
        .lightbox-container { display: flex; align-items: center; justify-content: center; height: 100vh; position: relative; }
        .lightbox-image { max-width: 90%; max-height: 90vh; object-fit: contain; }
        .lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); color: white; border: none; font-size: 40px; padding: 20px; cursor: pointer; border-radius: 50%; }
        .lightbox-nav:hover { background: rgba(255,255,255,0.4); }
        .lightbox-close { position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.2); color: white; border: none; font-size: 30px; padding: 10px 15px; cursor: pointer; border-radius: 50%; }
        .lightbox-info { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.7); color: white; padding: 10px 20px; border-radius: 8px; }
        .prev-btn { left: 20px; }
        .next-btn { right: 20px; }
    </style>
</head>
<body>
    <div class="lightbox-container">
        <button class="lightbox-close" onclick="window.location='{{ route('gallery.index') }}'">&times;</button>
        @if($prev)
            <a href="{{ route('gallery.lightbox', $prev->id) }}" class="lightbox-nav prev-btn">&lt;</a>
        @endif
        @if($next)
            <a href="{{ route('gallery.lightbox', $next->id) }}" class="lightbox-nav next-btn">&gt;</a>
        @endif
        <img src="{{ asset('uploads/' . $image->filename) }}" alt="{{ $image->original_filename ?? $image->filename }}" class="lightbox-image">
        <div class="lightbox-info">
            <strong>{{ $image->original_filename ?? $image->filename }}</strong><br>
            <small>{{ $image->formatted_size }} | {{ $image->created_at->format('M d, Y H:i') }}</small>
        </div>
    </div>
</body>
</html>
