<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Image::forUser($user->id)->active()->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('tag')) {
            $query->filterByTag($request->tag);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('filename', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('filename', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'size_asc':
                    $query->orderBy('size', 'asc');
                    break;
                case 'size_desc':
                    $query->orderBy('size', 'desc');
                    break;
            }
        }

        $perPage = $request->input('per_page', 12);
        $images = $query->paginate($perPage)->withQueryString();

        $allTags = Image::forUser($user->id)->active()
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->values()
            ->sort();

        return view('gallery', compact('images', 'allTags'));
    }

    public function trash(Request $request)
    {
        $user = Auth::user();
        $query = Image::forUser($user->id)->trashed()->orderByDesc('deleted_at');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $images = $query->paginate(12)->withQueryString();

        return view('trash', compact('images'));
    }

    public function restore($id)
    {
        $image = Image::forUser(Auth::id())->trashed()->findOrFail($id);
        $image->update(['is_deleted' => false, 'deleted_at' => null]);

        return redirect()->route('gallery.trash')->with('success', 'Image restored successfully!');
    }

    public function forceDelete($id)
    {
        $image = Image::forUser(Auth::id())->trashed()->findOrFail($id);
        $this->deleteImageFile($image->filename);
        $image->forceDelete();

        return redirect()->route('gallery.trash')->with('success', 'Image permanently deleted!');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:images,id',
        ]);

        $images = Image::forUser(Auth::id())->active()->whereIn('id', $request->ids)->get();

        foreach ($images as $image) {
            $image->update(['is_deleted' => true, 'deleted_at' => now()]);
        }

        return redirect()->route('gallery.index')->with('success', count($images) . ' images moved to trash!');
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:images,id',
        ]);

        $images = Image::forUser(Auth::id())->trashed()->whereIn('id', $request->ids)->get();

        foreach ($images as $image) {
            $image->update(['is_deleted' => false, 'deleted_at' => null]);
        }

        return redirect()->route('gallery.trash')->with('success', count($images) . ' images restored!');
    }

    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:images,id',
        ]);

        $images = Image::forUser(Auth::id())->trashed()->whereIn('id', $request->ids)->get();

        foreach ($images as $image) {
            $this->deleteImageFile($image->filename);
            $image->forceDelete();
        }

        return redirect()->route('gallery.trash')->with('success', count($images) . ' images permanently deleted!');
    }

    public function updateTags(Request $request, $id)
    {
        $image = Image::forUser(Auth::id())->findOrFail($id);
        $tags = array_filter(array_map('trim', explode(',', $request->tags)));
        $image->update(['tags' => array_values($tags)]);

        return back()->with('success', 'Tags updated successfully!');
    }

    public function updateCaption(Request $request, $id)
    {
        $image = Image::forUser(Auth::id())->findOrFail($id);
        $request->validate(['caption' => 'nullable|string|max:500']);
        $image->update(['caption' => $request->caption]);

        return back()->with('success', 'Caption updated successfully!');
    }

    public function rename(Request $request, $id)
    {
        $image = Image::forUser(Auth::id())->findOrFail($id);
        $request->validate([
            'new_filename' => 'required|string|max:255|regex:/^[a-zA-Z0-9_\-\.]+$/',
        ]);

        $oldPath = public_path('uploads/' . $image->filename);
        $newFilename = $request->new_filename;
        $newPath = public_path('uploads/' . $newFilename);

        if (File::exists($newPath)) {
            return back()->with('error', 'A file with this name already exists.');
        }

        if (File::exists($oldPath)) {
            File::move($oldPath, $newPath);
        }

        $image->update(['filename' => $newFilename]);

        return back()->with('success', 'Image renamed successfully!');
    }

    public function share($id)
    {
        $image = Image::forUser(Auth::id())->active()->findOrFail($id);
        $shareToken = Str::random(32);
        $metadata = $image->metadata ?? [];
        $metadata['share_token'] = $shareToken;
        $metadata['shared_at'] = now()->toIso8601String();
        $image->update(['metadata' => $metadata]);

        $shareUrl = route('image.shared', ['token' => $shareToken]);

        return back()->with('success', 'Shareable link: ' . $shareUrl);
    }

    public function shared($token)
    {
        $image = Image::active()->where('metadata->share_token', $token)->firstOrFail();
        return view('shared', compact('image'));
    }

    public function download($id)
    {
        $image = Image::forUser(Auth::id())->active()->findOrFail($id);
        return $this->downloadImage($image);
    }

    public function export($id, $format = 'png')
    {
        $image = Image::forUser(Auth::id())->active()->findOrFail($id);
        $filePath = public_path('uploads/' . $image->filename);

        if (!File::exists($filePath)) {
            return back()->with('error', 'Image file not found.');
        }

        $exportFormats = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array(strtolower($format), $exportFormats)) {
            return back()->with('error', 'Unsupported export format.');
        }

        $imageResource = imagecreatefromstring(File::get($filePath));
        if (!$imageResource) {
            return back()->with('error', 'Unable to process image.');
        }

        $tempFile = storage_path('app/temp/' . pathinfo($image->filename, PATHINFO_FILENAME) . '.' . $format);
        if (!File::exists(dirname($tempFile))) {
            File::makeDirectory(dirname($tempFile), 0755, true);
        }

        $mimeType = 'image/' . $format;
        if ($format === 'jpg' || $format === 'jpeg') {
            imagejpeg($imageResource, $tempFile, 90);
            $mimeType = 'image/jpeg';
        } elseif ($format === 'png') {
            imagepng($imageResource, $tempFile);
        } elseif ($format === 'webp') {
            imagewebp($imageResource, $tempFile, 90);
        } elseif ($format === 'gif') {
            imagegif($imageResource, $tempFile);
        }

        imagedestroy($imageResource);

        return Response::download($tempFile, pathinfo($image->filename, PATHINFO_FILENAME) . '.' . $format, [
            'Content-Type' => $mimeType,
        ])->deleteFileAfterSend(true);
    }

    public function print($id)
    {
        $image = Image::forUser(Auth::id())->active()->findOrFail($id);
        return view('print', compact('image'));
    }

    public function lightbox($id)
    {
        $image = Image::forUser(Auth::id())->active()->findOrFail($id);
        $next = Image::forUser(Auth::id())->active()->where('id', '>', $id)->orderBy('id')->first();
        $prev = Image::forUser(Auth::id())->active()->where('id', '<', $id)->orderByDesc('id')->first();

        return view('lightbox', compact('image', 'next', 'prev'));
    }

    private function deleteImageFile($filename)
    {
        $filePath = public_path('uploads/' . $filename);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    private function downloadImage($image)
    {
        $filePath = public_path('uploads/' . $image->filename);
        if (!File::exists($filePath)) {
            return back()->with('error', 'Image not found.');
        }
        return Response::download($filePath, $image->original_filename ?? $image->filename);
    }
}
