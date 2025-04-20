<?php

namespace App\Mail\SalesRep;

use App\Models\SalesRepresentative;
use App\Models\WholesalesUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Scalar\String_;

class NewCustomer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    var SalesRepresentative $rep;
    var WholesalesUser $customer;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SalesRepresentative $rep, WholesalesUser $customer)
    {
        $this->rep = $rep;
        $this->customer = $customer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.sales_rep.new-customer',
            [
                'rep'=>$this->rep,
                'customer'=>$this->customer,
            ]
        )
            ->subject("🎉 A New Customer Has Been Linked to Your Account!");
    }
}
