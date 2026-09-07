<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Image</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <style>
        body { background: #f5f7fa; }
        .shared-card { max-width: 800px; margin: 40px auto; border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shared-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Shared Image</h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ asset('uploads/' . $image->filename) }}" alt="{{ $image->original_filename ?? $image->filename }}" style="max-width: 100%; max-height: 600px; border-radius: 8px;">
                <div class="mt-3">
                    <p class="text-muted">{{ $image->original_filename ?? $image->filename }}</p>
                    <p class="text-muted">Shared on {{ $image->metadata['shared_at'] ?? 'Unknown' }}</p>
                </div>
                <div class="mt-3">
                    <a href="{{ route('login') }}" class="btn btn-primary">Login to View Gallery</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
