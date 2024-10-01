<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredEvent
{
    use Dispatchable, SerializesModels;

    public $user;
    public $token; // Add the token


    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
}
