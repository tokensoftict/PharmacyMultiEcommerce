<?php

namespace App\Services\Order;

use App\Classes\ApplicationEnvironment;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\SalesRepresentative;
use App\Models\Stock;
use App\Models\SupermarketAdmin;
use App\Models\SupermarketUser;
use App\Models\WholesalesAdmin;
use App\Models\WholesalesUser;
use App\Traits\StockResourceHelper;
use Illuminate\Support\Arr;
use Ramsey\Collection\Collection;

class CreateOrderProductService
{
    use StockResourceHelper;

    private WholesalesUser | SupermarketUser | WholesalesAdmin | SupermarketAdmin | SalesRepresentative| bool  $checkOutUser;
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

        // Resolve selected options into detailed option objects
        $selectedOptionIds = $attributes['selected_options'] ?? [];
        $resolvedOptions = [];
        if (count($selectedOptionIds) > 0) {
            $rawResolved = $this->resolveSelectedOptions($attributes['stock'], $selectedOptionIds);
            foreach ($rawResolved as $opt) {
                $resolvedOptions[] = [
                    'id' => $opt['id'],
                    'name' => $opt['group_name'] ?? '',
                    'value' => $opt['name'] ?? '',
                    'price' => (float)($opt['price'] ?? 0),
                    'price_prefix' => $opt['price_prefix'] ?? '+',
                    'group_name' => $opt['group_name'] ?? '',
                    'value_name' => $opt['name'] ?? '',
                    'option_name' => $opt['group_name'] ?? '',
                    'selectedValue' => $opt['name'] ?? '',
                    'amount' => (float)($opt['price'] ?? 0),
                    'sign' => $opt['price_prefix'] ?? '+'
                ];
            }
        }
        $attributes['options'] = $resolvedOptions;

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
        $cartStocks = [];
        $this->checkOutUser->getCart()->each(function ($stock) use (&$cartStocks) {
            $attributes = [];
            $attributes['stock'] = $stock;
            $attributes['quantity'] = $stock->cart_quantity;
            $attributes['price'] = $stock->price;
            $attributes['selected_options'] = $stock->selected_options ?? [];
            $cartStocks [] = new OrderProduct(
                $this->formatOrderProductAttributes($attributes)
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
