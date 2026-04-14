<?php

namespace App\Livewire\Backend\Admin\Order;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderTotalOrder;
use App\Models\Coupon;
use App\Models\VoucherCode;
use App\Services\Order\CreateOrderService;
use Illuminate\Support\Collection;
use Livewire\Component;

class EditOrderProduct extends Component
{

    public Order $order;
    public array $products;
    public array $removedStock = [];

    public function mount()
    {
        $this->removedStock = [];
        $this->products = $this->order->order_products->map(function ($item) {
            return ['id' => $item->id, "name" => $item->name, "total" => $item->total, 'quantity' => $item->quantity, 'price' => $item->price, "error" => $item->error];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.backend.admin.order.edit-order-product');
    }

    public function saveChanges()
    {
        if(count($this->products) > 0) {
            $status =  \DB::transaction(function () {

                if(count($this->removedStock) > 0) {
                    OrderProduct::whereIn("id", $this->removedStock)->delete();
                }

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
                    ->where(function($query){
                        $query->orWhere('name', 'Sub Total')
                            ->orWhere('name', 'Subtotal');
                    })->first();
                $orderTotal->value = $subTotal;
                $orderTotal->save();

                // Re-validate discounts based on new subtotal
                $discounts = $this->order->order_total_orders()
                    ->whereNotNull('discount_id')
                    ->get();

                foreach ($discounts as $discountRow) {
                    $isValid = true;
                    if ($discountRow->discount_type === 'Coupon') {
                        $coupon = Coupon::find($discountRow->discount_id);
                        if ($coupon && $coupon->minimum_amount > 0 && $subTotal < $coupon->minimum_amount) {
                            $isValid = false;
                        }
                    } elseif ($discountRow->discount_type === 'Voucher') {
                        $voucher = VoucherCode::find($discountRow->discount_id);
                        if ($voucher && $voucher->minimum_amount > 0 && $subTotal < $voucher->minimum_amount) {
                            $isValid = false;
                        }
                    }

                    if (!$isValid) {
                        $discountRow->delete();
                    }
                }

                // Refresh order total orders to ensure sums are correct
                $this->order = $this->order->refresh();


                //if this is door step delivery
                if($this->order->delivery_method->code === "Dsd") {
                    //coming right back for you
                }

                $total = $this->order->order_total_orders()->sum('value');

                $this->order->status_id = status("Submitted");
                $this->order->total = $total;
                $this->order->save();

                $this->alert(
                    "success",
                    "Order Product has been updated successfully",
                );

                $this->dispatch('refreshPage');

                return true;
            });

            if($status) {
                (new CreateOrderService())->orderItemChanged($this->order);
            }

            return $status;
        } else {
            $this->alert(
                "error",
                "No Item submitted, please try refresh the page",
            );
            return false;
        }
    }


    public function deleteItem($index) {
        $this->removedStock[] = $index;
        $this->products = $this->order->order_products->filter(function ($item) use ($index) {
            if($index != $item->id and !in_array($item->id, $this->removedStock)) {
                return ['id' => $item->id, "name" => $item->name, "total" => $item->total, 'quantity' => $item->quantity, 'price' => $item->price, "error" => $item->error];
            }
            return false;
        })->toArray();

    }

}
