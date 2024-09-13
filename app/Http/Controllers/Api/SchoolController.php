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
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class SchoolController extends Controller
{
    use AuthorizesRequests; // Add this line to import the trait
    public function index()
    {
        $this->authorize('viewAny', School::class);
        $schools = School::paginate(10);
        return SchoolResource::collection($schools);
    }

    public function store(StoreSchoolRequest $schoolRequest, RegisterUserRequest $userRequest)
    {
        $this->authorize('create', School::class);

        $schoolData = $schoolRequest->validated();
        $userData = $userRequest->validated();

        DB::beginTransaction();

        try {
            $school = School::create($schoolData);
            $user = User::create($userData);
            $school->user()->save($user);

            DB::commit();

            event(new UserRegisteredEvent($user, $userData['password']));

            return SchoolResource::make($school);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error creating school and user', 'error' => $e->getMessage()], 500);
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
        return SchoolResource::make($school);
    }

    public function destroy(School $school)
    {
        $this->authorize('delete', $school);
        $school->delete();
        return response()->json(null, 204);
    }

    public function search(SearchSchoolRequest $request)
    {
        $term = $request->input('search');
        $schools = School::search($term)->get();

        return SchoolResource::collection($schools);
    }
}