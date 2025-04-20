<?php

namespace App\Mail\Customer;

use App\Models\User;
use App\Models\WholesalesUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdministratorPasswordResetEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    var User $user;
    var String $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, String $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.customer.admin-password-reset-email',
            [
                'user'=>$this->user,
                'password'=>$this->password,
            ]
        )
            ->subject('Your Password Has Been Reset');
    }
}
