<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trash - Webcam Capture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f5f7fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
        .gallery-card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .image-card { border: 1px solid #ddd; border-radius: 10px; padding: 10px; background: #fff; height: 100%; }
        .gallery-image { width: 100%; height: 220px; object-fit: cover; border-radius: 8px; }
        .image-name { font-size: 13px; word-break: break-all; margin-top: 10px; color: #555; }
        .empty-state { text-align: center; padding: 60px 20px; color: #777; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('webcam.index') }}">📸 Webcam Capture</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('webcam.index') }}">Capture</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('gallery.index') }}">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('gallery.trash') }}">Trash</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit') }}">Profile</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
        @endif

        <form id="bulkForm" method="POST" action="{{ route('gallery.bulkRestore') }}">
            @csrf
            <div class="bulk-actions" id="bulkActions" style="display: none; background: #d4edda; border: 1px solid #28a745; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <strong><span id="selectedCount">0</span> images selected</strong>
                <button type="submit" class="btn btn-success btn-sm action-btn" onclick="return confirm('Restore selected images?')">Restore</button>
                <button type="button" class="btn btn-danger btn-sm action-btn" onclick="document.getElementById('deleteForm').submit(); return confirm('Permanently delete selected images?')">Delete Forever</button>
                <button type="button" class="btn btn-secondary btn-sm action-btn" onclick="clearSelection()">Cancel</button>
            </div>

            <div class="row">
                @forelse($images as $image)
                    <div class="col-md-4 mb-4">
                        <div class="image-card">
                            <input type="checkbox" name="ids[]" value="{{ $image->id }}" class="bulk-check" onchange="updateBulkActions()">
                            <img src="{{ asset('uploads/' . $image->filename) }}" alt="{{ $image->original_filename ?? $image->filename }}" class="gallery-image">
                            <div class="image-name">
                                <strong>File:</strong> {{ $image->filename }}<br>
                                <strong>Deleted:</strong> {{ $image->deleted_at->format('M d, Y H:i') }}
                            </div>
                            <div class="mt-2">
                                <form method="POST" action="{{ route('gallery.restore', $image->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success action-btn" onclick="return confirm('Restore this image?')">Restore</button>
                                </form>
                                <form method="POST" action="{{ route('gallery.forceDelete', $image->id) }}" class="d-inline" onsubmit="return confirm('Permanently delete this image? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger action-btn">Delete Forever</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <h5>🗑️ Trash is empty</h5>
                            <p>Deleted images will appear here.</p>
                            <a href="{{ route('gallery.index') }}" class="btn btn-primary">View Gallery</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $images->links() }}
            </div>
        </form>

        <form id="deleteForm" method="POST" action="{{ route('gallery.bulkForceDelete') }}" style="display: none;">
            @csrf
            <input type="hidden" name="ids" id="deleteIds">
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script>
        function updateBulkActions() {
            const checked = document.querySelectorAll('.bulk-check:checked').length;
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            if (checked > 0) {
                bulkActions.style.display = 'block';
                selectedCount.textContent = checked;
                const ids = Array.from(document.querySelectorAll('.bulk-check:checked')).map(cb => cb.value);
                document.getElementById('deleteIds').value = ids;
            } else {
                bulkActions.style.display = 'none';
            }
        }

        function clearSelection() {
            document.querySelectorAll('.bulk-check').forEach(cb => cb.checked = false);
            updateBulkActions();
        }
    </script>
</body>
</html>
