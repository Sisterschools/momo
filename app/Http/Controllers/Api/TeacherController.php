<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Events\UserRegisteredEvent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\TeacherResource;

class TeacherController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        // $this->authorize('viewAny', Teacher::class);
        $teachers = Teacher::paginate(10);
        return TeacherResource::collection($teachers);
    }

    public function store(StoreTeacherRequest $request)
    {
        $this->authorize('create', Teacher::class);

        DB::beginTransaction();

        try {
            // Create the user first
            $userData = $request->only(['name', 'email', 'password', 'role']);
            $userData['role'] = 'teacher';
            $user = User::create($userData);

            // Create the teacher
            $teacherData = $request->only(['name', 'photo', 'phone_number', 'bio']);
            $teacher = Teacher::create($teacherData);

            // Associate the user with the teacher
            $teacher->user()->save($user);

            // Attach the teacher to selected schools
            $teacher->schools()->attach($request->school_ids);

            // Dispatch UserRegisteredEvent
            event(new UserRegisteredEvent($user, $userData['password']));

            DB::commit();

            return TeacherResource::make($teacher);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['message' => 'Error creating teacher', 'errors' => $e->getMessage()], 500);
        }
    }
}
