<?php

namespace App\Mail\Administrator;

use App\Models\SalesRepresentative;
use App\Models\SupermarketAdmin;
use App\Models\WholesalesAdmin;
use App\Models\WholesalesUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Scalar\String_;

class AdministratorInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    var WholesalesAdmin | SupermarketAdmin $rep;
    var String $link;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(WholesalesAdmin | SupermarketAdmin $rep, String  $link)
    {
        $this->rep = $rep;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.admin.invitation',
            [
                'rep'=>$this->rep,
                'link'=>$this->link,
            ]
        )
            ->subject("🌟 You've Been Invited to Join Our Admin Team!");
    }
}
