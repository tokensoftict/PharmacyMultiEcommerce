<?php

namespace App\Events\Auth;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class EmailVerified
{
    use SerializesModels;

    public User $user;
    /**
     * Create a new event instance.
     */
    public function __construct(User $user)
    {
        $user->verification_token = NULL;
        $user->email_verification_pin = NULL;
        $user->update();
        $this->user = $user;
    }

}
