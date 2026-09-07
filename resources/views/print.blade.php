<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print - {{ $image->original_filename ?? $image->filename }}</title>
    <style>
        body { margin: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        img { max-width: 100%; max-height: 100vh; }
        @media print {
            body { margin: 0; }
            img { max-width: 100%; max-height: 100vh; }
        }
    </style>
</head>
<body onload="window.print()">
    <img src="{{ asset('uploads/' . $image->filename) }}" alt="{{ $image->original_filename ?? $image->filename }}">
</body>
</html>
