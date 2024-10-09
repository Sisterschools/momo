<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Http\Requests\School\StoreSchoolRequest;
use App\Events\UserRegisteredEvent;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\School\SearchSchoolRequest;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\SchoolCollection;
use App\Http\Resources\StudentCollection;
use App\Http\Resources\TeacherCollection;
use App\Http\Requests\School\AttachStudentsToSchoolRequest;
use App\Http\Requests\School\AttachTeachersToSchoolRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class SchoolController extends Controller
{
    use AuthorizesRequests; // Add this line to import the trait
    public function index()
    {
        $this->authorize('viewAny', School::class);
        $schools = School::paginate();
        return SchoolCollection::make($schools);
    }

    public function store(StoreSchoolRequest $request)
    {
        $this->authorize('create', School::class);



        DB::beginTransaction();

        try {

            // Create the user first
            $userData = $request->only(['name', 'email', 'password', 'role']);
            $userData['role'] = 'school';
            $user = User::create($userData);


            // Create the school
            $schoolData = $request->only([
                'title',
                'photo',
                'address',
                'description',
                'phone_number',
                'website',
                'founding_year',
                'student_capacity'
            ]);
            $school = School::create($schoolData);


            $school->user()->save($user);
            event(new UserRegisteredEvent($user, $userData['password']));

            DB::commit();

            return SchoolResource::make($school);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error creating school and user',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function show(School $school)
    {
        $this->authorize('view', $school);
        return SchoolResource::make($school);
    }

    public function update(UpdateSchoolRequest $request, School $school)
    {
        $this->authorize('update', $school);
        $school->update($request->validated());
        return response()->json([
            'message' => 'School updated successfully.',
            'data' => SchoolResource::make($school)
        ]);
    }

    public function destroy(School $school)
    {
        $this->authorize('delete', $school);
        $school->delete();
        return response()->json(null, 204);
    }

    public function search(SearchSchoolRequest $request)
    {
        $term = $request->query('search');
        $schools = School::search($term)->paginate()->appends(['search' => $term]); // Paginate search results with 10 items per page

        return SchoolCollection::make($schools);
    }


    // Attach students to school
    public function attachStudentsToSchool(AttachStudentsToSchoolRequest $request, School $school)
    {
        $this->authorize('update', $school); // You may want to create a policy for this
        $studentIds = $request->validated()['student_ids'];
        $school->students()->syncWithoutDetaching($studentIds); // Attach without detaching existing students
        return response()->json([
            'message' => 'Students attached to the school successfully.',
            'attached_student_count' => count($studentIds)
        ]);
    }

    // Attach teachers to school
    public function attachTeachersToSchool(AttachTeachersToSchoolRequest $request, School $school)
    {
        $this->authorize('update', $school); // You may want to create a policy for this
        $teacherIds = $request->validated()['teacher_ids'];
        $school->teachers()->syncWithoutDetaching($teacherIds); // Attach without detaching existing teachers
        return response()->json([
            'message' => 'Teachers attached to the school successfully.',
            'attached_teacher_count' => count($teacherIds)
        ]);
    }

    // List all students in school
    public function listStudents(School $school)
    {
        $this->authorize('view', $school);

        $students = $school->students()->paginate(); // You can adjust pagination as needed

        return StudentCollection::make($students);

    }

    // List all teachers in school
    public function listTeachers(School $school)
    {
        $this->authorize('view', $school);

        $teachers = $school->teachers()->paginate(); // You can adjust pagination as needed

        return TeacherCollection::make($teachers);

    }

}