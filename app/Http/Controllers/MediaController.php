<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * List all uploaded media files.
     */
    public function index()
    {
        $files = Storage::disk('public')->files('uploads');
        $images = [];

        foreach ($files as $file) {
            $mime = Storage::disk('public')->mimeType($file);
            if (Str::startsWith($mime, 'image/')) {
                $images[] = [
                    'url' => Storage::url($file),
                    'path' => $file,
                    'name' => basename($file),
                ];
            }
        }

        // Sort by newest first (this is a simple file system sort, DB would be better but this is lightweight)
        return response()->json(array_reverse($images));
    }

    /**
     * Upload a new media file.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:5120', // 5MB max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');

            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path,
                'message' => 'Image uploaded successfully'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }
}
