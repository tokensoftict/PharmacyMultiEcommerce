<?php

namespace App\Livewire\Backend\Admin\Order;

use App\Models\Order;
use App\Services\ImportOrderService;
use App\Services\Order\CreateOrderService;
use Illuminate\Support\Facades\DB;
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
        DB::transaction(function () {
            $this->order->status_id = status('Submitted');
            $this->order->save();
            (new CreateOrderService())->processOrder($this->order);
            $this->alert(
                "success",
                "Order has been re-packed successfully",
            );

            $this->dispatch('refreshPage');
        });
    }


    public function reLoadOrder()
    {
        DB::transaction(function () {
            $importOrderService = app(ImportOrderService::class);

            $order = \App\Models\Old\Order::with(['user','address','address.zone','orderStatus','orderTotalOrders','orderProducts','paymentMethod','shippingMethod','shippingAddress','shippingAddress.zone'])
                ->where(function($query){
                    $query->orWhere('id', $this->order->local_order_id)->orWhere('invoice_no', $this->order->invoice_no);
                })->first();

            $importOrderService->handle($order->toArray(), true);
        });
    }
}
