<?php
namespace App\Observers;

use App\Models\Teacher;
use App\Services\PhotoUploadService;

class TeacherObserver
{
    protected $photoUploadService;

    public function __construct(PhotoUploadService $photoUploadService)
    {
        $this->photoUploadService = $photoUploadService;
    }

    public function creating(Teacher $teacher)
    {
        if (request()->hasFile('photo')) {
            // Use the service to upload the photo
            $teacher->photo = $this->photoUploadService->uploadPhoto(request()->file('photo'));
        }
    }

    public function updating(Teacher $teacher)
    {
        if (request()->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($teacher->getOriginal('photo')) {
                $this->photoUploadService->deletePhoto($teacher->getOriginal('photo'));
            }

            // Upload the new photo
            $teacher->photo = $this->photoUploadService->uploadPhoto(request()->file('photo'));
        }
    }

    public function deleting(Teacher $teacher)
    {
        if ($teacher->photo) {
            // Delete the photo when the teacher is deleted
            $this->photoUploadService->deletePhoto($teacher->photo);
        }
    }
}
