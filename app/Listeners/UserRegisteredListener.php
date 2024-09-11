<?php
namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegisteredMail;
use Illuminate\Queue\InteractsWithQueue;

class UserRegisteredListener implements ShouldQueue
{
    use InteractsWithQueue;
    public function handle(UserRegisteredEvent $event)
    {
        Log::info('UserRegisteredListener listener triggered for user: ' . $event->user->email);

        // Your email sending logic here
        Mail::to($event->user->email)->send(new UserRegisteredMail($event->user, $event->password));




    }

}
