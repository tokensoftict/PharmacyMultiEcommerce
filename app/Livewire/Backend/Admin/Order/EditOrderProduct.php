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
                    $orderProduct = OrderProduct::find($product['id']);
                    $quantity = (int) $product['quantity'];
                    $stock = \App\Models\Stock::find($orderProduct->stock_id);

                    $price = $product['price'];

                    if ($stock && $orderProduct->quantity != $quantity) {
                        $app = \App\Models\App::find($this->order->app_id);
                        $department = ($app && $app->id == 6) ? 'retail' : 'wholesales';
                        $stockModelString = ($app && $app->id == 6) ? "supermarkets_stock_prices" : "wholessales_stock_prices";
                        
                        $defaultPrice = ($stock->special === false ? $stock->{$stockModelString}->price : $stock->special);
                        $customPrices = $stock->stockquantityprices->where('department', $department);
                        
                        if ($customPrices->count() > 0) {
                            $price = $defaultPrice;
                            foreach ($customPrices as $priceRule) {
                                $min = (int)$priceRule['min_qty'];
                                $max = (int)$priceRule['max_qty'];

                                if ($department == "wholesales") {
                                    $carton = $stock->carton > 0 ? $stock->carton : 1;
                                    if (($quantity / $carton) >= $min && ($quantity / $carton) < $max) {
                                        $price = (float)$priceRule['price'];
                                        break;
                                    }
                                } else {
                                    if ($quantity >= $min && $quantity < $max) {
                                        $price = (float)$priceRule['price'];
                                        break;
                                    }
                                }
                            }
                            
                            // Re-apply options if present
                            $cartCache = $this->order->cart_cache ?? [];
                            $options = $cartCache[$orderProduct->stock_id]['options'] ?? [];
                            if (count($options) > 0) {
                                $adjustment = 0;
                                $priceField = ($department === 'retail') ? 'retail_price' : 'wholesales_price';
                                $prefixField = ($department === 'retail') ? 'retail_price_prefix' : 'wholesales_price_prefix';

                                foreach ($stock->stock_option_values as $optionValue) {
                                    foreach ($optionValue->options as $option) {
                                        if (in_array($option['id'], $options)) {
                                            $optPrice = (float)($option[$priceField] ?? 0);
                                            $prefix = $option[$prefixField] ?? '+';
                                            if ($prefix === '+') $adjustment += $optPrice;
                                            else if ($prefix === '-') $adjustment -= $optPrice;
                                        }
                                    }
                                }
                                $price += $adjustment;
                            }
                        }
                    }

                    $orderProduct->update([
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $price * $quantity,
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
                if ($orderTotal) {
                    $orderTotal->value = $subTotal;
                    $orderTotal->save();
                }

                // Re-validate discounts based on new subtotal
                $discounts = $this->order->order_total_orders()
                    ->whereNotNull('discount_id')
                    ->get();

                foreach ($discounts as $discountRow) {
                    $isValid = true;
                    $discountModel = null;
                    if ($discountRow->discount_type === 'Coupon') {
                        $discountModel = Coupon::find($discountRow->discount_id);
                    } elseif ($discountRow->discount_type === 'Voucher') {
                        $discountModel = VoucherCode::find($discountRow->discount_id);
                    }

                    if ($discountModel && $discountModel->minimum_amount > 0 && $subTotal < $discountModel->minimum_amount) {
                        $isValid = false;
                    }

                    if (!$isValid) {
                        $discountRow->delete();
                    } elseif ($discountModel) {
                        $value = 0;
                        if ($discountModel->type == 'Percentage') {
                            $value = ($discountModel->type_value / 100) * $subTotal;
                            $value = ceil($value);
                            $value = -$value;
                        } else {
                            $value = $discountModel->type_value;
                            if ($value > $subTotal) {
                                $value = $subTotal;
                            }
                            $value = -$value;
                        }
                    $discountRow->value = $value;
                        $discountRow->save();
                    }
                }

                // Re-validate Membership Discount
                $membershipDiscountRow = $this->order->order_total_orders()
                    ->where('name', 'like', 'Membership Discount%')
                    ->first();

                if ($membershipDiscountRow) {
                    $department = ($this->order->app_id == 6) ? 'retail' : 'wholesales';
                    $user = $this->order->customer?->user;
                    $memberGroup = ($department === 'retail') ? $user?->retailMemberGroup : $user?->memberGroup;

                    $isValid = false;
                    if ($memberGroup && $memberGroup->status && $memberGroup->member_discount > 0) {
                        $isExpired = false;
                        if ($memberGroup->discount_until) {
                            $isExpired = \Carbon\Carbon::parse($memberGroup->discount_until)->isPast();
                        }
                        if (!$isExpired) {
                            $isValid = true;
                        }
                    }

                    if (!$isValid) {
                        $membershipDiscountRow->delete();
                    } else {
                        $membershipDiscountValue = ($memberGroup->member_discount / 100) * $subTotal;
                        $membershipDiscountRow->value = -$membershipDiscountValue;
                        $membershipDiscountRow->name = "Membership Discount (" . strtoupper($memberGroup->label ?? $memberGroup->name) . ") - " . $memberGroup->member_discount . "%";
                        $membershipDiscountRow->save();
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
