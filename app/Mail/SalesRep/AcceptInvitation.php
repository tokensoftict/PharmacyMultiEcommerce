<?php

namespace App\Mail\SalesRep;

use App\Models\SalesRepresentative;
use App\Models\WholesalesUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Scalar\String_;

class AcceptInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    var SalesRepresentative $rep;
    var String $link;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SalesRepresentative $rep)
    {
        $this->rep = $rep;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.sales_rep.accept-invitation',
            [
                'rep'=>$this->rep,
            ]
        )
            ->subject(" ✅ You’re Officially a Sales Representative!");
    }
}
