<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebcamController extends Controller
{
    /**
     * Show webcam view page
     */
    public function index()
    {
        return view('webcam');
    }

    /**
     * Store Base64 webcam image directly into public/uploads folder
     */
    public function store(Request $request)
    {
        // Get base64 image
        $img = $request->image;

        // Folder path in PUBLIC directory
        $folderPath = public_path('uploads/');

        // Create folder if not exists
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Split base64
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];

        // Decode image
        $image_base64 = base64_decode($image_parts[1]);

        // File name
        $fileName = uniqid() . '.png';

        // Final full path
        $file = $folderPath . $fileName;

        // Save file manually
        file_put_contents($file, $image_base64);

        dd("Image uploaded successfully: uploads/" . $fileName);
    }
}
