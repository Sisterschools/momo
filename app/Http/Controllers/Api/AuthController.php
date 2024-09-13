<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Resources\UserResource;
use App\Events\UserRegisteredEvent;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    use AuthorizesRequests; // Add this line to import the trait

    public function register(RegisterUserRequest $request)
    {
        $this->authorize('register', Auth::user());

        $data = $request->validated();
        $user = User::create($data);
        event(new UserRegisteredEvent($user, $data['password']));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => UserResource::make($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        // Update the user's password
        Auth::user()->update([
            'password' => $request->new_password,
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    public function login(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Delete tokens older than a month
        $user->tokens()->where('created_at', '<', Carbon::now()->subMonth())->delete();

        // At this point, the password has already been validated by the request
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => UserResource::make($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function unauthenticated()
    {
        return response()->json([
            "status" => false,
            "message" => "Unauthenticated. Please login first",
        ], 401);
    }
}