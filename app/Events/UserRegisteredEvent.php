<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredEvent
{
    use Dispatchable, SerializesModels;

    public $user;
    public $password; // Add the password


    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }
}
