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
use App\Http\Requests\User\PasswordSetupRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use AuthorizesRequests; // Add this line to import the trait

    public function register(RegisterUserRequest $request)
    {
        $this->authorize('register', Auth::user());

        $data = $request->validated();
        $data['password'] = Hash::make(str()->random(10));// Temporary password

        $user = User::create($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        // This should create the token in your `password_reset_tokens` table
        $passwored_token = Password::createToken($user);
        // Trigger event to send a url to the user to setup their password
        event(new UserRegisteredEvent($user, $passwored_token));


        return response()->json([
            'data' => UserResource::make($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // Password Setup for first time through email
    public function passwordSetup(PasswordSetupRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been setup successfully.'], 200)
            : response()->json(['message' => 'Invalid token.'], 400);
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

    /**
     * @unauthenticated
     */
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
            "message" => "Unauthenticated. Please login first",
        ], 401);
    }
}