<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Webcam Capture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f5f7fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
        .gallery-card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .image-card { border: 1px solid #ddd; border-radius: 10px; padding: 10px; background: #fff; height: 100%; transition: transform 0.2s; }
        .image-card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
        .gallery-image { width: 100%; height: 220px; object-fit: cover; border-radius: 8px; cursor: pointer; }
        .image-name { font-size: 13px; word-break: break-all; margin-top: 10px; color: #555; }
        .tag { font-size: 11px; padding: 2px 8px; border-radius: 12px; background: #e9ecef; color: #495057; }
        .tag.active { background: #007bff; color: white; }
        .sidebar { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); height: fit-content; }
        .bulk-actions { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .empty-state { text-align: center; padding: 60px 20px; color: #777; }
        .stats-card { border-radius: 10px; padding: 15px; text-align: center; }
        .action-btn { padding: 4px 8px; font-size: 12px; margin: 2px; }
        .dark-mode-toggle { cursor: pointer; }
        body.dark-mode { background: #1a1a2e; color: #e0e0e0; }
        body.dark-mode .card, body.dark-mode .sidebar { background: #16213e; color: #e0e0e0; }
        body.dark-mode .image-card { background: #0f3460; border-color: #533483; }
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('gallery.trash') }}">Trash</a></li>
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

        <div class="row">
            <div class="col-md-3">
                <div class="sidebar">
                    <h5>Filters</h5>
                    <form method="GET" action="{{ route('gallery.index') }}">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search images...">
                        </div>
                        <div class="form-group">
                            <label>Sort By</label>
                            <select name="sort" class="form-control" onchange="this.form.submit()">
                                <option value="">Default</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest)</option>
                                <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest)</option>
                                <option value="size_asc" {{ request('sort') == 'size_asc' ? 'selected' : '' }}>Size (Small)</option>
                                <option value="size_desc" {{ request('sort') == 'size_desc' ? 'selected' : '' }}>Size (Large)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Per Page</label>
                            <select name="per_page" class="form-control" onchange="this.form.submit()">
                                <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12</option>
                                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                                <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
                            </select>
                        </div>
                        @if(request('search')) <a href="{{ route('gallery.index') }}" class="btn btn-secondary btn-block">Clear Filters</a> @endif
                    </form>
                </div>

                @if($allTags->count() > 0)
                    <div class="sidebar mt-3">
                        <h5>Tags</h5>
                        <div class="d-flex flex-wrap">
                            @foreach($allTags as $tag)
                                <a href="{{ route('gallery.index', array_merge(request()->query(), ['tag' => $tag])) }}" class="tag mr-1 mb-1 {{ request('tag') == $tag ? 'active' : '' }}">{{ $tag }}</a>
                            @endforeach
                        </div>
                        @if(request('tag')) <a href="{{ route('gallery.index') }}" class="btn btn-sm btn-secondary mt-2">Clear Tag</a> @endif
                    </div>
                @endif
            </div>

            <div class="col-md-9">
                <form id="bulkForm" method="POST" action="{{ route('gallery.bulkDelete') }}">
                    @csrf
                    <div class="bulk-actions" id="bulkActions" style="display: none;">
                        <strong><span id="selectedCount">0</span> images selected</strong>
                        <button type="submit" class="btn btn-danger btn-sm action-btn" onclick="return confirm('Move selected to trash?')">Delete</button>
                        <button type="button" class="btn btn-secondary btn-sm action-btn" onclick="clearSelection()">Cancel</button>
                    </div>

                    <div class="row">
                        @forelse($images as $image)
                            <div class="col-md-4 mb-4">
                                <div class="image-card">
                                    <input type="checkbox" name="ids[]" value="{{ $image->id }}" class="bulk-check" onchange="updateBulkActions()">
                                    <img src="{{ asset('uploads/' . $image->filename) }}" alt="{{ $image->original_filename ?? $image->filename }}" class="gallery-image" onclick="window.location='{{ route('gallery.lightbox', $image->id) }}'">
                                    <div class="image-name">
                                        <strong>File:</strong> {{ $image->filename }}<br>
                                        <strong>Size:</strong> {{ $image->formatted_size }}<br>
                                        <strong>Date:</strong> {{ $image->created_at->format('M d, Y H:i') }}
                                        @if($image->caption) <br><em>{{ $image->caption }}</em> @endif
                                    </div>
                                    <div class="mt-2">
                                        @foreach($image->tags as $tag)
                                            <span class="tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('gallery.download', $image->id) }}" class="btn btn-sm btn-primary action-btn"><i class="fas fa-download"></i></a>
                                        <a href="{{ route('gallery.lightbox', $image->id) }}" class="btn btn-sm btn-info action-btn"><i class="fas fa-expand"></i></a>
                                        <a href="{{ route('gallery.print', $image->id) }}" class="btn btn-sm btn-secondary action-btn"><i class="fas fa-print"></i></a>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-success action-btn dropdown-toggle" data-toggle="dropdown">Export</button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('gallery.export', ['id' => $image->id, 'format' => 'jpg']) }}">JPG</a>
                                                <a class="dropdown-item" href="{{ route('gallery.export', ['id' => $image->id, 'format' => 'png']) }}">PNG</a>
                                                <a class="dropdown-item" href="{{ route('gallery.export', ['id' => $image->id, 'format' => 'webp']) }}">WEBP</a>
                                                <a class="dropdown-item" href="{{ route('gallery.export', ['id' => $image->id, 'format' => 'gif']) }}">GIF</a>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-warning action-btn" onclick="showShareModal({{ $image->id }})"><i class="fas fa-share-alt"></i></button>
                                        <form method="POST" action="{{ route('gallery.tags', $image->id) }}" class="d-inline">
                                            @csrf
                                            <input type="text" name="tags" class="form-control d-inline" style="width: 100px; display: inline;" placeholder="Add tags" value="{{ implode(', ', $image->tags ?? []) }}">
                                            <button type="submit" class="btn btn-sm btn-outline-primary action-btn"><i class="fas fa-tags"></i></button>
                                        </form>
                                        <button class="btn btn-sm btn-outline-dark action-btn" onclick="showRenameModal({{ $image->id }}, '{{ $image->filename }}')"><i class="fas fa-edit"></i></button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="empty-state">
                                    <h5>📭 No images found</h5>
                                    <p>Capture some images to see them here.</p>
                                    <a href="{{ route('webcam.index') }}" class="btn btn-primary">Capture Image</a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $images->links() }}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shareModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share Image</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="shareForm" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" id="shareUrl" class="form-control" readonly>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Generate Link</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="renameModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename Image</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="renameForm" method="POST">
                        @csrf
                        <input type="text" id="newFilename" class="form-control" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitRename()">Rename</button>
                </div>
            </div>
        </div>
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
            } else {
                bulkActions.style.display = 'none';
            }
        }

        function clearSelection() {
            document.querySelectorAll('.bulk-check').forEach(cb => cb.checked = false);
            updateBulkActions();
        }

        function showShareModal(id) {
            $('#shareForm').attr('action', '/gallery/share/' + id);
            $('#shareModal').modal('show');
        }

        function showRenameModal(id, filename) {
            $('#renameForm').attr('action', '/gallery/rename/' + id);
            $('#newFilename').val(filename);
            $('#renameModal').modal('show');
        }

        function submitRename() {
            const filename = document.getElementById('newFilename').value;
            const action = document.getElementById('renameForm').attr('action');
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'new_filename';
            input.value = filename;
            form.appendChild(csrf);
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        $('#shareForm').on('submit', function(e) {
            e.preventDefault();
            const url = document.getElementById('shareUrl').value;
            if (url) {
                navigator.clipboard.writeText(url).then(() => alert('Link copied to clipboard!'));
            }
        });
    </script>
</body>
</html>
