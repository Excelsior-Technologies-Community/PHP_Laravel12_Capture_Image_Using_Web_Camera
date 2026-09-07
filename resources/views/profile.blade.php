<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Webcam Capture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <style>
        body { background: #f5f7fa; }
        .profile-card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .stats-card { border-radius: 10px; padding: 20px; text-align: center; }
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
                    <li class="nav-item"><a class="nav-link active" href="{{ route('profile.edit') }}">Profile</a></li>
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

        <div class="row">
            <div class="col-md-4">
                <div class="card profile-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card stats-card bg-primary text-white mb-3">
                            <h3>{{ $stats['total_images'] }}</h3>
                            <p>Total Images</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stats-card bg-success text-white mb-3">
                            <h3>{{ $stats['active_images'] }}</h3>
                            <p>Active Images</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stats-card bg-warning text-dark mb-3">
                            <h3>{{ $stats['trashed_images'] }}</h3>
                            <p>Trashed Images</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stats-card bg-info text-white mb-3">
                            <h3>{{ number_format($stats['storage_used'] / 1024, 2) }} KB</h3>
                            <p>Storage Used</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
