<?php

namespace App\Services\Order;

use App\Classes\ApplicationEnvironment;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Stock;
use App\Models\SupermarketUser;
use App\Models\WholesalesUser;
use Illuminate\Support\Arr;
use Ramsey\Collection\Collection;

class CreateOrderProductService
{
    private SupermarketUser|WholesalesUser|bool|null  $checkOutUser = null;
    public function __construct()
    {
        $this->checkOutUser = getApplicationModel();
    }

    /**
     * @param array $attributes
     * @return array
     */
    private function formatOrderProductAttributes(array $attributes) : array
    {
        if(!isset($attributes['stock'])) {
            throw new \Exception("stock is required to create an order product");
        }

        $attributes['order_product_id'] = $attributes['order_product_id'] ?? generateUniqueid();
        $attributes['stock_id'] = $attributes['stock']->id ?? throw new \Exception("stock is required to create an order product");
        $attributes['local_id'] = $attributes['stock']->local_stock_id ?? throw new \Exception("stock is required to create an order product");
        $attributes['name'] =  $attributes['name'] ?? $attributes['stock']->name;
        $attributes['model'] = $attributes['model'] ?? $attributes['stock']->model ?? "No model";
        $attributes['quantity'] = $attributes['quantity'] ?? 1;
        $attributes['price'] =  $attributes['price'] ?? ($attributes['stock']->special ?:  $attributes['stock']->{ApplicationEnvironment::$stock_model_string}->price);
        $attributes['total'] = $attributes['quantity'] * $attributes['price'];
        $attributes['tax'] =  $attributes['tax'] ?? 0;
        $attributes['reward'] = $attributes['reward'] ?? 5;
        $attributes['app_id'] = ApplicationEnvironment::$id;
        $attributes['sales_representative_id'] = $this->checkOutUser->sales_representative_id ?? NULL;

        return $attributes;
    }

    /**
     * @param Order|int $order
     * @return Order
     * @throws \Exception
     */
    public final function create(Order|int $order) : Order
    {
        $orderPrepareProduct = $this->prepareOrderProduct();
        $order->order_products()->saveMany($orderPrepareProduct);

        return $order->fresh();
    }


    public final function createOrderProductFromOldServer(Order|int $order, array $orderProduct) : Order
    {
        $orderPrepareProduct = $this->prepareOrderFromOldServer($orderProduct);
        $order->order_products()->saveMany($orderPrepareProduct);

        return $order->fresh();
    }


    /**
     * @return array
     * @throws \Exception
     */
    public final function prepareOrderProduct() : array
    {
        $cart =  $this->checkOutUser->cart;
        $stockIds = array_keys($cart);
        $cartStocks = [];
        Stock::whereIn('id', $stockIds)->get()->each(function ($stock) use (&$stockIds, &$cartStocks, &$cart) {
            $stockIds['stock'] = $stock;
            $stockIds['quantity'] = $cart[$stock->id]['quantity'];
            $cartStocks [] = new OrderProduct(
                $this->formatOrderProductAttributes($stockIds)
            );
        });

        return $cartStocks;
    }


    /**
     * @param array $orderProduct
     * @return array
     * @throws \Exception
     */
    public final function prepareOrderFromOldServer(array $orderProduct) : array
    {
        $stockInOrder = [];
        foreach ($orderProduct as $product) {
            $stockInOrder[$product['local_id']] = Arr::only($product, ['quantity', 'price', 'name', 'model','total', 'tax', 'reward', 'created_at', 'updated_at']);
        }

        $cartStocks = [];
        Stock::whereIn('local_stock_id', array_keys($stockInOrder))->get()->each(function ($stock) use (&$stockInOrder,&$cartStocks) {
            $stockIds = $stockInOrder[$stock->local_stock_id];
            $stockIds['stock'] = $stock;
            $cartStocks [] = new OrderProduct(
                $this->formatOrderProductAttributes($stockIds)
            );
        });

        return $cartStocks;
    }
}
