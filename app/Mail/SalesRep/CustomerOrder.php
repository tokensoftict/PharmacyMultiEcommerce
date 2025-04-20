<?php

namespace App\Mail\SalesRep;

use App\Models\Order;
use App\Models\SalesRepresentative;
use App\Models\WholesalesUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Scalar\String_;

class CustomerOrder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    var SalesRepresentative $rep;
    var Order $order;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SalesRepresentative $rep, Order $order)
    {
        $this->rep = $rep;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.sales_rep.customer_order',
            [
                'rep'=>$this->rep,
                'order'=>$this->order,
            ]
        )
            ->subject("🛒 You’ve Got a New Order Through Your Referral!");
    }
}
