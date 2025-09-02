<?php
namespace App\Traits;

use App\Classes\ApplicationEnvironment;
use App\Http\Resources\Api\Stock\StockInCartResource;
use App\Http\Resources\Api\Stock\StockInWishlistResource;
use App\Models\DeliveryMethod;
use App\Models\OrderTotal;
use App\Models\PaymentMethod;
use App\Models\Stock;
use App\Repositories\DsdRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

trait ApplicationUserCheckoutTrait
{
    /**
     * @return int
     */
    public final function calculateShoppingCartTotal() : int
    {
        if(is_null($this->cart)) return 0;

        $shoppingCart = $this->cart;

        if(count($shoppingCart) == 0) {
            return 0;
        }

        $stockPriceModel = ApplicationEnvironment::$stock_model_string;

        $stockIDs = array_keys($shoppingCart);
        $stocks = Stock::with([$stockPriceModel])->whereIn('id', $stockIDs)->get();
        return  $stocks->sum(function($stock) use($shoppingCart, $stockPriceModel){
            $price = ($stock->special === false ? $stock->{$stockPriceModel}->price : $stock->special);
            if($stock->stockquantityprices->count() > 0 and ApplicationEnvironment::$stock_model_string == "supermarkets_stock_prices") {
                $price = $this->resolvePriceByQuantity($shoppingCart[$stock->id]['quantity'], $price, $stock->stockquantityprices->toArray());
            }
            return $price * $shoppingCart[$stock->id]['quantity'];
        });
    }


    /**
     * @return array
     */
    public final function getWishlistItems() : AnonymousResourceCollection
    {
        $wishlist = $this->wishlist ?? [];
        $stocks = Stock::whereKey(array_keys($wishlist))->get();

        $stocks = $stocks->map(function($stock) use ($wishlist){
            $price = ($stock->special === false ? $stock->{ApplicationEnvironment::$stock_model_string}->price : $stock->special);
            $stock->added_date = $wishlist[$stock->id]['date'];
            $stock->price = $price;
            return $stock;
        });

        return StockInWishlistResource::collection($stocks);
    }

    public final function getCart() : Collection
    {
        $cart = $this->cart ?? [];
        $stocks = Stock::whereKey(array_keys($cart))->get();
        $totalItemsInCarts = 0;
        return $stocks->map(function($stock) use ($cart, &$totalItemsInCarts){
            $price = ($stock->special === false ? $stock->{ApplicationEnvironment::$stock_model_string}->price : $stock->special);
            if($stock->stockquantityprices->count() > 0 and ApplicationEnvironment::$stock_model_string == "supermarkets_stock_prices") {
                $price = $this->resolvePriceByQuantity($cart[$stock->id]['quantity'], $price, $stock->stockquantityprices->toArray());
            }
            $stock->cart_quantity = $cart[$stock->id]['quantity'];
            $stock->added_date = $cart[$stock->id]['date'];
            $stock->price = $price;
            $stock->total = ($cart[$stock->id]['quantity'] * $price);
            $totalItemsInCarts+= ($cart[$stock->id]['quantity'] * $price);
            return $stock;
        });
    }

    public final function resolvePriceByQuantity(int $quantity, float $defaultSellingPrice, array $customPrices): float
    {
        foreach ($customPrices as $priceRule) {
            $min = (int) $priceRule['min_qty'];
            $max = (int) $priceRule['max_qty'];

            if ($quantity >= $min && $quantity < $max) {
                return (float) $priceRule['price'];
            }
        }

        return $defaultSellingPrice;
    }

    /**
     * @return array
     */
    public final function getShoppingCartItems() : array
    {
        $cart = $this->cart ?? [];
        $stocks = Stock::whereKey(array_keys($cart))->get();
        $totalItemsInCarts = 0;
        $stocks = $stocks->map(function($stock) use ($cart, &$totalItemsInCarts){
            $price = ($stock->special === false ? $stock->{ApplicationEnvironment::$stock_model_string}->price : $stock->special);
            if($stock->stockquantityprices->count() > 0 and ApplicationEnvironment::$stock_model_string == "supermarkets_stock_prices") {
                $price = $this->resolvePriceByQuantity($cart[$stock->id]['quantity'], $price, $stock->stockquantityprices->toArray());
            }
            $stock->cart_quantity = $cart[$stock->id]['quantity'];
            $stock->added_date = $cart[$stock->id]['date'];
            $stock->price = $price;
            $stock->total = ($cart[$stock->id]['quantity'] * $price);
            $totalItemsInCarts+= ($cart[$stock->id]['quantity'] * $price);
            return $stock;
        });


        $meta = [
            "noItems" => $stocks->count(),
            "totalItemsInCarts" =>$totalItemsInCarts,
            'totalItemsInCarts_formatted' => money($totalItemsInCarts)
        ];

        $calculateDoorStepDelivery = (new DsdRepository())->calculateDeliveryTotal($cart,
            DeliveryMethod::find(7), []
        );

        if($calculateDoorStepDelivery['status'] === true) {
            $meta['doorStepDelivery'] = $calculateDoorStepDelivery;
        }

        return [
            "items" =>StockInCartResource::collection($stocks),
            "meta" => $meta
        ];
    }


    /**
     * @param int|NULL $shoppingCartSubTotal
     * @return array
     */
    public final function getUserCheckoutSubTotal(int $shoppingCartSubTotal = NULL) : array
    {
        $subTotal =$shoppingCartSubTotal ?? $this->calculateShoppingCartTotal();
        $items[] = [
            "name" =>  "Sub Total",
            "amount" => $subTotal,
            "amount_formatted" => money($subTotal),
            "disabled" => true,
            "autoCheck" => true
        ];

        return [
            'status' => true,
            'items' => $items,
            'total' => $subTotal,
            'total_formatted' => money($subTotal)
        ];
    }
    /**
     * @return array
     */
    public final function getUserCheckOutOrderTotal(int $shoppingCartSubTotal = NULL) : array
    {
        $removeOrderTotal = $this->remove_order_total ?? [];
        $subTotal = $shoppingCartSubTotal ?? $this->calculateShoppingCartTotal();
        $orderTotalList = [];

        $totalOfOrderTotal = OrderTotal::where('status', "1")->get();
        $totalOfOrderTotal = $totalOfOrderTotal->sum(function($orderTotal) use($subTotal, $removeOrderTotal, &$orderTotalList) {
            if($orderTotal->order_total_type == "Percentage") {
                $amount = (((float)$orderTotal->value / 100) * $subTotal);
                $orderTotalList[] = [
                    "name" => $orderTotal->title. "[".$orderTotal->value."%]",
                    "amount" => $amount,
                    "amount_formatted" =>  money($amount),
                    "id" => $orderTotal->id,
                    "autoCheck" => !in_array($orderTotal->id, $removeOrderTotal),
                    "disabled" => false
                ];
            } else {
                $amount = (int)$orderTotal->value;
                $orderTotalList[] = [
                    "name" => $orderTotal->title. "[".money($amount)."]",
                    "amount" => $amount,
                    "amount_formatted" => money($amount),
                    "id" => $orderTotal->id,
                    "autoCheck" => !in_array($orderTotal->id, $removeOrderTotal),
                    "disabled" => false
                ];
            }
            return !in_array($orderTotal->id, $removeOrderTotal) ? $amount : 0;
        });

        return [
            'status' => true,
            'items' => $orderTotalList,
            'total' => $totalOfOrderTotal,
            'total_formatted' => money($totalOfOrderTotal)
        ];
    }


    /**
     * @param float|int $total
     * @return array|false[]
     */
    public final function calculatePayStackCharges(float|int $total) : array
    {
        $paymentMethod = PaymentMethod::find($this->getCheckoutPaymentMethod());
        $paystackCharges =[];
        if($paymentMethod and $paymentMethod->code === "Paystack") {
            $paymentMethodRepository = "App\\Repositories\\".ucwords(strtolower($paymentMethod->code))."Repository";
            $paymentMethodRepository = new $paymentMethodRepository();
            $charges = $paymentMethodRepository->calculateCharges($total);

            $paystackCharges[] = [
                "name" =>  $charges['name'],
                "amount" => $charges['amount'],
                "amount_formatted" => money($charges['amount']),
                "disabled" => false,
                "autoCheck" => true
            ];

            return [
                'status' => true,
                'items' => $paystackCharges,
                'total' => $charges['amount'],
                'total_formatted' => money($charges['amount'])
            ];

        }
        return ['status' =>false];
    }


    /**
     * @return array
     */
    public final function getUserCheckDeliveryTotal() : array
    {
        $deliveryMethod = $this->checkout['deliveryMethod']['deliveryMethod'];
        if(!$deliveryMethod) {
            return [
                'status' => false,
                'message' => "Unable able to determine your delivery Method"
            ];
        }


        $shoppingCart = $this->cart;
        $deliveryItems = [];

        //calculate the delivery Cost
        $methodOfDelivery = DeliveryMethod::findorfail($deliveryMethod);
        //call the associated delivery repository to calculate delivery cost
        $code = $methodOfDelivery->code;
        $deliveryRepository = "App\\Repositories\\".ucwords(strtolower($code))."Repository";
        $deliveryRepository = new $deliveryRepository();
        $deliveryTotal = $deliveryRepository->calculateDeliveryTotal($shoppingCart, $methodOfDelivery ,$this->checkout['deliveryMethod']['extraData']);

        if($deliveryTotal['status'] === false) return [
            'status' =>false,
            'message' => $deliveryTotal['error']
        ];

        $deliveryItems[] = [
            "name" =>  $deliveryTotal['name']." - ".money($deliveryTotal['amount']),
            "amount" => $deliveryTotal['amount'],
            "amount_formatted" => money($deliveryTotal['amount']),
            "disabled" => true,
            "autoCheck" => true
        ];

        return [
            'status' => true,
            'items' => $deliveryItems,
            'total' => $deliveryTotal['amount'],
            'total_formatted' => money($deliveryTotal['amount'])
        ];
    }
}
