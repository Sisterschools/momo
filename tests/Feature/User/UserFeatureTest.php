<?php


namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\UserRegisteredMail;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use App\Events\UserRegisteredEvent;
use Illuminate\Support\Facades\Queue;
use App\Listeners\UserRegisteredListener;
use Illuminate\Events\CallQueuedListener;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase; // Ensures the database is refreshed between tests

    /**
     * Test user login.
     *
     * @return void
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
                'access_token',
                'token_type',
            ]);
    }

    public function test_user_can_login_and_old_tokens_are_deleted()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Create some old tokens
        $oldToken1 = $user->createToken('old_token_1');
        $oldToken2 = $user->createToken('old_token_2');

        // Set the created_at date of these tokens to be older than a month
        $oldDate = Carbon::now()->subMonths(2);
        PersonalAccessToken::where('id', $oldToken1->accessToken->id)->update(['created_at' => $oldDate]);
        PersonalAccessToken::where('id', $oldToken2->accessToken->id)->update(['created_at' => $oldDate]);

        // Create a recent token
        $recentToken = $user->createToken('recent_token');

        // Perform login
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert the response is successful and contains a token
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
            'access_token',
            'token_type',
        ]);

        // Assert old tokens are deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $oldToken1->accessToken->id,
        ]);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $oldToken2->accessToken->id,
        ]);

        // Assert recent token still exists
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $recentToken->accessToken->id,
        ]);

        // Assert a new token was created
        $this->assertEquals(2, $user->tokens()->count()); // Recent token + newly created token
    }

    /**
     * Test user password update.
     *
     * @return void
     */
    public function test_user_can_update_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->patchJson('/api/users/password', [
            'current_password' => 'password123',
            'new_password' => 'newpassword456',
            'new_password_confirmation' => 'newpassword456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password updated successfully.',
            ]);

        $this->assertTrue(Hash::check('newpassword456', $user->fresh()->password));
    }


    /**
     * Test admin can register a user and send email.
     *
     * @return void
     */
    public function test_admin_can_register_user()
    {

        Mail::fake();
        Queue::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'sanctum');

        // Perform the registration
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        // Assert the response and structure
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'updated_at'
                ],
                'access_token',
                'token_type',
            ]);

        // Assert the user is in the database
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

    }

    /**
     * Test the UserRegistered event is fired when a user is registered.
     *
     * @return void
     */
    public function test_user_registered_event_is_fired()
    {
        // Fake events
        Event::fake();
        // Create an admin user and authenticate
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'sanctum');

        // Perform the registration
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',

            'role' => 'student',
        ]);

        // Assert that the UserRegistered event was dispatched
        Event::assertDispatched(UserRegisteredEvent::class, function ($event) {
            return $event->user->email === 'john@example.com';
        });
    }



    /**
     * Test that the UserRegisteredListener sends an email when the event is handled.
     *
     * @return void
     */
    public function test_user_registered_listener_sends_email()
    {
        // Fake mail and queue
        Mail::fake();
        Event::fake();
        Queue::fake();

        // Create a user and dispatch the event
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $event = new UserRegisteredEvent($user, 'password123');

        // Manually trigger the listener
        resolve(UserRegisteredListener::class)->handle($event);

        // Assert that an email was sent
        Mail::assertSent(UserRegisteredMail::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
    }

    /**
     * Test that the UserRegisteredListener is queued when the event is fired.
     *
     * @return void
     */
    public function test_user_registered_listener_is_queued()
    {
        // Fake the queue
        Queue::fake();

        // Create an admin user and authenticate
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'sanctum');

        // Perform the registration
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        // Assert that the listener's job was queued as a CallQueuedListener
        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            // Check if the job contains the correct listener and event
            return $job->class === UserRegisteredListener::class
                && $job->data[0]->user->email === 'john@example.com';
        });
    }

    /**
     * Test non-admin cannot register a user.
     *
     * @return void
     */
    public function test_non_admin_cannot_register_user()
    {
        // Create a non-admin user and authenticate
        $nonAdmin = User::factory()->create([
            'role' => 'student',
        ]);

        $this->actingAs($nonAdmin, 'sanctum');

        // Try to register a user
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        // Assert the response is unauthorized (403)
        $response->assertStatus(403);
    }

    /**
     * Test user cannot register with invalid role.
     *
     * @return void
     */
    public function test_admin_cannot_register_with_invalid_role()
    {
        // Create an admin user and authenticate
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'sanctum');

        // Try to register a user with an invalid role
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'invalid_role',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }



    /**
     * Test admin can update other users' data.
     *
     * @return void
     */
    public function test_admin_can_update_other_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $token = $admin->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/users/' . $user->id, [
                    'name' => 'Admin Updated Name',
                ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Admin Updated Name',
                ],
            ]);
    }

    /**
     * Test admin can delete a user.
     *
     * @return void
     */
    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $token = $admin->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Test regular user cannot delete another user.
     *
     * @return void
     */
    public function test_regular_user_cannot_delete_another_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/users/' . $otherUser->id);

        $response->assertStatus(403); // Forbidden, since only admins can delete
    }

    /**
     * Test regular user cannot delete themselves.
     *
     * @return void
     */
    public function test_regular_user_cannot_delete_themselves()
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(403); // Forbidden, since only admins can delete
    }

    /**
     * Test regular user cannot update other users' data.
     *
     * @return void
     */
    public function test_regular_user_cannot_update_other_user()
    {
        $user = User::factory()->create();       // Regular user
        $otherUser = User::factory()->create();  // Another user

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/users/' . $otherUser->id, [
                    'name' => 'Malicious Update',
                ]);

        $response->assertStatus(403); // Forbidden, since regular users can't update other users
    }
}
