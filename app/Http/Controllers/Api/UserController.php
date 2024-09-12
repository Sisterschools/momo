<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class UserController extends Controller
{
    use AuthorizesRequests; // Add this line to import the trait

    // List all users
    public function index()
    {

        $users = User::paginate(10);
        return UserResource::collection($users);
    }

    // Show a single user
    public function show(User $user)
    {
        return UserResource::make($user);
    }


    // Update a user's details
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        // Get validated data
        $data = $request->validated();

        $user->update($data);

        // Return a response
        return response()->json([
            'message' => 'User updated successfully.',
            'data' => UserResource::make($user)
        ]);

    }

    // Delete a user
    public function destroy(User $user)
    {
        // Authorize the action
        $this->authorize('delete', $user);

        // Delete user's tokens
        $user->tokens()->delete();

        $user->delete();

        return response()->json(null, 204);

    }


    public function getRoles()
    {
        return response()->json(User::getRoles());
    }
}
