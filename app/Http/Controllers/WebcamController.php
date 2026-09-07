<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class WebcamController extends Controller
{
    /**
     * Show webcam page with captured image gallery.
     */
    public function index()
    {
        $folderPath = public_path('uploads');

        // Create uploads folder if it doesn't exist
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true);
        }

        // Get all captured images
        $images = collect(File::files($folderPath))
            ->filter(function ($file) {
                return in_array(
                    strtolower($file->getExtension()),
                    ['jpg', 'jpeg', 'png']
                );
            })
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            });

        return view('webcam', compact('images'));
    }

    /**
     * Store captured/edited Base64 image.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        $img = $request->image;

        /*
        |--------------------------------------------------------------------------
        | Validate Base64 Image
        |--------------------------------------------------------------------------
        */

        if (!preg_match('/^data:image\/(jpeg|jpg|png);base64,/', $img)) {
            return redirect()
                ->route('webcam.index')
                ->with('error', 'Invalid image format.');
        }

        /*
        |--------------------------------------------------------------------------
        | Extract Base64 Data
        |--------------------------------------------------------------------------
        */

        $imageParts = explode(';base64,', $img);

        if (count($imageParts) !== 2) {
            return redirect()
                ->route('webcam.index')
                ->with('error', 'Invalid image data.');
        }

        $imageBase64 = base64_decode($imageParts[1], true);

        if ($imageBase64 === false) {
            return redirect()
                ->route('webcam.index')
                ->with('error', 'Unable to process the image.');
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Folder
        |--------------------------------------------------------------------------
        */

        $folderPath = public_path('uploads');

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true);
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Unique Filename
        |--------------------------------------------------------------------------
        */

        $fileName = 'webcam_' . uniqid() . '.png';

        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        /*
        |--------------------------------------------------------------------------
        | Save Image
        |--------------------------------------------------------------------------
        */

        file_put_contents($filePath, $imageBase64);

        return redirect()
            ->route('webcam.index')
            ->with('success', 'Image captured and saved successfully!');
    }

    /**
     * Download captured image.
     */
    public function download($filename)
    {
        // Prevent directory traversal
        $filename = basename($filename);

        $filePath = public_path('uploads/' . $filename);

        if (!File::exists($filePath)) {
            return redirect()
                ->route('webcam.index')
                ->with('error', 'Image not found.');
        }

        return Response::download($filePath, $filename);
    }

    /**
     * Delete captured image.
     */
    public function destroy($filename)
    {
        // Prevent directory traversal
        $filename = basename($filename);

        $filePath = public_path('uploads/' . $filename);

        if (!File::exists($filePath)) {
            return redirect()
                ->route('webcam.index')
                ->with('error', 'Image not found.');
        }

        File::delete($filePath);

        return redirect()
            ->route('webcam.index')
            ->with('success', 'Image deleted successfully!');
    }
}