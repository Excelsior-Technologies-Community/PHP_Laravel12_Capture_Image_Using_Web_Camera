<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class WebcamController extends Controller
{
    public function index()
    {
        $folderPath = public_path('uploads');
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true);
        }

        $images = collect(File::files($folderPath))
            ->filter(function ($file) {
                return in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            })
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            });

        return view('webcam', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate(['image' => 'required|string']);

        $img = $request->image;
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp|gif);base64,/', $img)) {
            return redirect()->route('webcam.index')->with('error', 'Invalid image format.');
        }

        $imageParts = explode(';base64,', $img);
        if (count($imageParts) !== 2) {
            return redirect()->route('webcam.index')->with('error', 'Invalid image data.');
        }

        $imageBase64 = base64_decode($imageParts[1], true);
        if ($imageBase64 === false) {
            return redirect()->route('webcam.index')->with('error', 'Unable to process the image.');
        }

        $folderPath = public_path('uploads');
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true);
        }

        $extension = 'png';
        if (preg_match('/^data:image\/(\w+);base64,/', $img, $matches)) {
            $extension = $matches[1];
            if ($extension === 'jpeg') $extension = 'jpg';
        }

        $fileName = 'webcam_' . uniqid() . '.' . $extension;
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        file_put_contents($filePath, $imageBase64);

        $imageSize = File::size($filePath);
        $imageInfo = getimagesize($filePath);
        $width = $imageInfo[0] ?? null;
        $height = $imageInfo[1] ?? null;

        if (Auth::check()) {
            Image::create([
                'user_id' => Auth::id(),
                'filename' => $fileName,
                'original_filename' => 'capture_' . date('Ymd_His') . '.' . $extension,
                'mime_type' => mime_content_type($filePath),
                'size' => $imageSize,
                'width' => $width,
                'height' => $height,
                'tags' => [],
                'caption' => null,
                'is_deleted' => false,
                'metadata' => [],
            ]);
        }

        return redirect()->route('webcam.index')->with('success', 'Image captured and saved successfully!');
    }

    public function download($filename)
    {
        $filename = basename($filename);
        $filePath = public_path('uploads/' . $filename);
        if (!File::exists($filePath)) {
            return redirect()->route('webcam.index')->with('error', 'Image not found.');
        }
        return Response::download($filePath, $filename);
    }

    public function destroy($filename)
    {
        $filename = basename($filename);
        $filePath = public_path('uploads/' . $filename);
        if (!File::exists($filePath)) {
            return redirect()->route('webcam.index')->with('error', 'Image not found.');
        }
        File::delete($filePath);
        return redirect()->route('webcam.index')->with('success', 'Image deleted successfully!');
    }
}
