<?php

namespace App\Livewire\Backend\Admin\Order;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Collection;
use Livewire\Component;

class EditOrderProduct extends Component
{

    public Order $order;
    public array $products;

    public function mount()
    {
        $this->products = $this->order->order_products->map(function ($item) {
            return ['id' => $item->id, 'quantity' => $item->quantity, 'price' => $item->price];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.backend.admin.order.edit-order-product');
    }



    public function saveChanges()
    {
        if(count($this->products) > 0) {
            return \DB::transaction(function () {
                foreach ($this->products as $product) {
                    OrderProduct::find($product['id'])->update([
                        'quantity' => $product['quantity'],
                        'total' => $product['price'] * $product['quantity'],
                    ]);
                }

                $this->order = $this->order->refresh();

                $subTotal = $this->order->order_products()->get()->sum(function ($item) {
                   return $item['quantity'] * $item['price'];
                });

                $orderTotal = $this->order->order_total_orders()->where('order_id', $this->order->id)
                    ->where('name', 'Sub Total')->first();
                $orderTotal->value = $subTotal;
                $orderTotal->save();


                $total = $this->order->order_total_orders()->sum('value');

                $this->order->status_id = status('Pending');
                $this->order->total = $total;
                $this->order->save();

                $this->alert(
                    "success",
                    "Order Product has been updated successfully",
                );

                $this->dispatch('refreshPage');

                return true;
            });
        }


    }

}
