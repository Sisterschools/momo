<?php
namespace App\Observers;

use App\Models\School;
use App\Services\PhotoUploadService;

class SchoolObserver
{
    protected $photoUploadService;

    public function __construct(PhotoUploadService $photoUploadService)
    {
        $this->photoUploadService = $photoUploadService;
    }

    public function creating(School $school)
    {
        if (request()->hasFile('photo')) {
            // Use the service to upload the photo
            $school->photo = $this->photoUploadService->uploadPhoto(request()->file('photo'));
        }
    }

    public function updating(School $school)
    {
        if (request()->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($school->getOriginal('photo')) {
                $this->photoUploadService->deletePhoto($school->getOriginal('photo'));
            }

            // Upload the new photo
            $school->photo = $this->photoUploadService->uploadPhoto(request()->file('photo'));
        }
    }

    public function deleting(School $school)
    {
        if ($school->photo) {
            // Delete the photo when the school is deleted
            $this->photoUploadService->deletePhoto($school->photo);
        }
    }
}
