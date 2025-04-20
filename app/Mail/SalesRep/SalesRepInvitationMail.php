<?php

namespace App\Mail\SalesRep;

use App\Models\SalesRepresentative;
use App\Models\WholesalesUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Scalar\String_;

class SalesRepInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    var SalesRepresentative $rep;
    var String $link;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SalesRepresentative $rep, String  $link)
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
        return $this->view('mails.sales_rep.invitation',
            [
                'rep'=>$this->rep,
                'link'=>$this->link,
            ]
        )
            ->subject("🌟 You've Been Invited to Join Our Sales Team!");
    }
}
