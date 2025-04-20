<?php

namespace App\Mail\Customer;

use App\Models\WholesalesUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendActivationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    var WholesalesUser $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(WholesalesUser $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.customer.activated_account',
            [
                'user'=>$this->user
            ]
        )
            ->subject('Great News! Your Account Has Been Activated!');
    }
}
