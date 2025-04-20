<?php

namespace App\Http\Resources\Api\Order;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'invoiceNo' => "#".$this->invoice_no,
            'totalAmount' => money($this->total),
            'status' => $this->status->name,
            'products' => OrderProductResource::collection($this->order_products),
            'orderTotals' => OrderTotalResource::collection($this->order_total_orders),
            'address' => $this->address->full_address,
            'paymentMethod' => $this->payment_method->name,
            'itemsCount' => $this->order_products->count()
        ];
    }


}
