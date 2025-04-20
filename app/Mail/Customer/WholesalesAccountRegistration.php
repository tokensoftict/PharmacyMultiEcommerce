<?php

namespace App\Mail\Customer;

use App\Models\WholesalesUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WholesalesAccountRegistration extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    var WholesalesUser $user;
    var String $password = "";
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(WholesalesUser $user, String $password)
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
        return $this->view('mails.customer.wholesales',
            [
                'user'=>$this->user,
                'password'=>$this->password,
            ]
        )
            ->subject(' 🎉 Thanks for Registering! Your Wholesale Account Is Under Review');
    }
}
