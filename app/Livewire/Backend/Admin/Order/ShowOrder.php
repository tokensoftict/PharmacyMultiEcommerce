<?php

namespace App\Livewire\Backend\Admin\Order;

use App\Models\Order;
use Livewire\Component;

class ShowOrder extends Component
{

    public Order $order;


    public function render()
    {
        return view('livewire.backend.admin.order.show-order');
    }


    public function rePackOrder()
    {
        $this->order->status_id = status('Submitted');
        $this->order->save();
        $this->alert(
            "success",
            "Order has been re-packed successfully",
        );

        $this->dispatch('refreshPage');
    }
}
