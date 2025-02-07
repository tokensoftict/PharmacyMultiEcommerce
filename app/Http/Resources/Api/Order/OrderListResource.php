<?php

namespace App\Http\Resources\Api\Order;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'orderDate' => $this->order_date->format('d M, Y'),
            'orderId' => "#".$this->order_id,
            'total' => money($this->total),
            'payment_method' => $this->payment_method->name,
            'status' => $this->status->name,
            'image' => $this->getOrderImage()
        ];
    }


    private function getOrderImage(): string
    {
       $products = $this->order_products->filter(function (OrderProduct $orderProduct)  {
           return $orderProduct->stock->image !== NULL;
        });

       if($products->count() === 0) {;
           return $this->order_products->first()?->stock->product_image;
       } else {
          $products = $products->shuffle();
          return $products->first()?->stock->product_image;
       }
    }
}
