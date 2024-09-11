<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    /**
     * Test password hashing.
     *
     * @return void
     */
    public function test_password_is_hashed()
    {
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);
        
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
