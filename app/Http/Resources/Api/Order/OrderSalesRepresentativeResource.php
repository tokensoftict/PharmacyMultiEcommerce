<?php

namespace App\Http\Resources\Api\Order;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSalesRepresentativeResource extends JsonResource
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
            'orderId' => $this->order_id,
            'invoiceNo' => "#".$this->invoice_no,
            'totalAmount' => money($this->total),
            'status' => $this->status->name,
            'product_count' => $this->order_products->count(),
            'customer' => $this->customer,
            'paymentMethod' => $this->payment_method->name,
            'itemsCount' => $this->order_products->count()
        ];
    }


}
