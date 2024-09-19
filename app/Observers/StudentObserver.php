<?php
namespace App\Observers;

use App\Models\Student;
use App\Services\PhotoUploadService;

class StudentObserver
{
    protected $photoUploadService;

    public function __construct(PhotoUploadService $photoUploadService)
    {
        $this->photoUploadService = $photoUploadService;
    }

    public function creating(Student $student)
    {
        if (request()->hasFile('photo')) {
            // Use the service to upload the photo
            $student->photo = $this->photoUploadService->uploadPhoto(request()->file('photo'));
        }
    }

    public function updating(Student $student)
    {
        if (request()->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($student->getOriginal('photo')) {
                $this->photoUploadService->deletePhoto($student->getOriginal('photo'));
            }

            // Upload the new photo
            $student->photo = $this->photoUploadService->uploadPhoto(request()->file('photo'));
        }
    }

    public function deleting(Student $student)
    {
        if ($student->photo) {
            // Delete the photo when the student is deleted
            $this->photoUploadService->deletePhoto($student->photo);
        }
    }
}
