<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PhotoUploadService
{
    /**
     * Handle the upload of a photo.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function uploadPhoto(UploadedFile $file): string
    {
        $path = $file->store('photos', 'public');
        return $path;
    }

    /**
     * Delete the photo from storage.
     *
     * @param string $path
     * @return void
     */
    public function deletePhoto(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
