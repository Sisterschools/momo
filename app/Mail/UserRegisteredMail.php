<?php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        $url = config('app.url') . '/register?token=' . $this->token;

        return $this->subject('Welcome to MoMo Education Platform!')
            ->view('emails.user_registered')
            ->with(['url' => $url]);
    }
}