<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class WebcamController extends Controller
{
    /**
     * Show webcam page with image gallery.
     *
     * Features:
     * - Search
     * - Pagination
     * - Sorting
     * - Statistics
     */
    public function index(Request $request)
    {
        $folderPath = public_path('uploads');

        /*
        |--------------------------------------------------------------------------
        | Create uploads folder
        |--------------------------------------------------------------------------
        */

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true);
        }

        /*
        |--------------------------------------------------------------------------
        | Get Images
        |--------------------------------------------------------------------------
        */

        $allImages = collect(File::files($folderPath))
            ->filter(function ($file) {
                return in_array(
                    strtolower($file->getExtension()),
                    ['jpg', 'jpeg', 'png', 'webp']
                );
            });

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalImages = $allImages->count();

        $jpgImages = $allImages->filter(function ($file) {
            return in_array(
                strtolower($file->getExtension()),
                ['jpg', 'jpeg']
            );
        })->count();

        $pngImages = $allImages->filter(function ($file) {
            return strtolower($file->getExtension()) === 'png';
        })->count();

        $webpImages = $allImages->filter(function ($file) {
            return strtolower($file->getExtension()) === 'webp';
        })->count();

        $totalStorage = $allImages->sum(function ($file) {
            return $file->getSize();
        });

        $formattedStorage = $this->formatBytes($totalStorage);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = trim($request->get('search', ''));

        if ($search !== '') {
            $allImages = $allImages->filter(function ($file) use ($search) {
                return stripos($file->getFilename(), $search) !== false;
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Sort
        |--------------------------------------------------------------------------
        */

        $sort = $request->get('sort', 'newest');

        switch ($sort) {

            case 'oldest':

                $allImages = $allImages->sortBy(function ($file) {
                    return $file->getMTime();
                });

                break;

            case 'name_asc':

                $allImages = $allImages->sortBy(function ($file) {
                    return strtolower($file->getFilename());
                });

                break;

            case 'name_desc':

                $allImages = $allImages->sortByDesc(function ($file) {
                    return strtolower($file->getFilename());
                });

                break;

            case 'size_asc':

                $allImages = $allImages->sortBy(function ($file) {
                    return $file->getSize();
                });

                break;

            case 'size_desc':

                $allImages = $allImages->sortByDesc(function ($file) {
                    return $file->getSize();
                });

                break;

            case 'newest':
            default:

                $allImages = $allImages->sortByDesc(function ($file) {
                    return $file->getMTime();
                });

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Convert Collection To Simple Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = 6;

        $currentPage = max(
            1,
            (int) $request->get('page', 1)
        );

        $totalFilteredImages = $allImages->count();

        $pagedImages = $allImages
            ->slice(
                ($currentPage - 1) * $perPage,
                $perPage
            )
            ->values();

        $lastPage = max(
            1,
            (int) ceil($totalFilteredImages / $perPage)
        );

        /*
        |--------------------------------------------------------------------------
        | Keep Current Page Valid
        |--------------------------------------------------------------------------
        */

        if ($currentPage > $lastPage) {
            $currentPage = $lastPage;

            $pagedImages = $allImages
                ->slice(
                    ($currentPage - 1) * $perPage,
                    $perPage
                )
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination Data
        |--------------------------------------------------------------------------
        */

        $pagination = [
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'total' => $totalFilteredImages,
            'per_page' => $perPage,
        ];

        return view('webcam', compact(
            'pagedImages',
            'pagination',
            'search',
            'sort',
            'totalImages',
            'jpgImages',
            'pngImages',
            'webpImages',
            'totalStorage',
            'formattedStorage'
        ));
    }


    /**
     * Store captured/edited Base64 image.
     *
     * Duplicate images are prevented using SHA-256 hash.
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

        if (!preg_match(
            '/^data:image\/(jpeg|jpg|png);base64,/',
            $img
        )) {
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

        $imageBase64 = base64_decode(
            $imageParts[1],
            true
        );

        if ($imageBase64 === false) {
            return redirect()
                ->route('webcam.index')
                ->with('error', 'Unable to process the image.');
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Prevention
        |--------------------------------------------------------------------------
        */

        $newImageHash = hash(
            'sha256',
            $imageBase64
        );

        $folderPath = public_path('uploads');

        if (!File::exists($folderPath)) {
            File::makeDirectory(
                $folderPath,
                0777,
                true
            );
        }

        $existingImages = File::files($folderPath);

        foreach ($existingImages as $existingImage) {

            if (!in_array(
                strtolower($existingImage->getExtension()),
                ['jpg', 'jpeg', 'png', 'webp']
            )) {
                continue;
            }

            $existingContent = File::get(
                $existingImage->getPathname()
            );

            $existingHash = hash(
                'sha256',
                $existingContent
            );

            if ($existingHash === $newImageHash) {

                return redirect()
                    ->route('webcam.index')
                    ->with(
                        'error',
                        'Duplicate image detected. This image is already saved.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Unique Filename
        |--------------------------------------------------------------------------
        */

        $fileName =
            'webcam_' .
            date('Ymd_His') .
            '_' .
            uniqid() .
            '.png';

        $filePath =
            $folderPath .
            DIRECTORY_SEPARATOR .
            $fileName;

        /*
        |--------------------------------------------------------------------------
        | Save Image
        |--------------------------------------------------------------------------
        */

        file_put_contents(
            $filePath,
            $imageBase64
        );

        return redirect()
            ->route('webcam.index')
            ->with(
                'success',
                'Image captured and saved successfully!'
            );
    }


    /**
     * Download captured image.
     */
    public function download($filename)
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent Directory Traversal
        |--------------------------------------------------------------------------
        */

        $filename = basename($filename);

        $filePath =
            public_path('uploads/' . $filename);

        if (!File::exists($filePath)) {

            return redirect()
                ->route('webcam.index')
                ->with(
                    'error',
                    'Image not found.'
                );
        }

        return Response::download(
            $filePath,
            $filename
        );
    }


    /**
     * Delete single captured image.
     */
    public function destroy($filename)
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent Directory Traversal
        |--------------------------------------------------------------------------
        */

        $filename = basename($filename);

        $filePath =
            public_path('uploads/' . $filename);

        if (!File::exists($filePath)) {

            return redirect()
                ->route('webcam.index')
                ->with(
                    'error',
                    'Image not found.'
                );
        }

        File::delete($filePath);

        return redirect()
            ->route('webcam.index')
            ->with(
                'success',
                'Image deleted successfully!'
            );
    }


    /**
     * Bulk delete selected images.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'string',
        ]);

        $deletedCount = 0;

        foreach ($request->images as $filename) {

            /*
            |--------------------------------------------------------------------------
            | Prevent Directory Traversal
            |--------------------------------------------------------------------------
            */

            $filename = basename($filename);

            $filePath =
                public_path('uploads/' . $filename);

            if (File::exists($filePath)) {

                File::delete($filePath);

                $deletedCount++;
            }
        }

        if ($deletedCount === 0) {

            return redirect()
                ->route('webcam.index')
                ->with(
                    'error',
                    'No images were deleted.'
                );
        }

        return redirect()
            ->route('webcam.index')
            ->with(
                'success',
                $deletedCount .
                    ' image(s) deleted successfully!'
            );
    }


    /**
     * Delete all images.
     */
    public function destroyAll()
    {
        $folderPath =
            public_path('uploads');

        if (!File::exists($folderPath)) {

            return redirect()
                ->route('webcam.index')
                ->with(
                    'error',
                    'Uploads folder not found.'
                );
        }

        $deletedCount = 0;

        $files = File::files($folderPath);

        foreach ($files as $file) {

            if (in_array(
                strtolower($file->getExtension()),
                ['jpg', 'jpeg', 'png', 'webp']
            )) {

                File::delete(
                    $file->getPathname()
                );

                $deletedCount++;
            }
        }

        return redirect()
            ->route('webcam.index')
            ->with(
                'success',
                $deletedCount .
                    ' image(s) deleted successfully!'
            );
    }


    /**
     * Format bytes.
     */
    private function formatBytes($bytes)
    {
        if ($bytes <= 0) {
            return '0 Bytes';
        }

        $units = [
            'Bytes',
            'KB',
            'MB',
            'GB',
            'TB'
        ];

        $power = floor(
            log($bytes, 1024)
        );

        $power = min(
            $power,
            count($units) - 1
        );

        return round(
            $bytes / pow(1024, $power),
            2
        ) . ' ' . $units[$power];
    }
}
