<?php

namespace App\Services;

use App\Classes\ApplicationEnvironment;
use App\Models\Address;
use App\Models\App;
use App\Models\Order;
use App\Models\SalesRepresentative;
use App\Models\State;
use App\Models\SupermarketUser;
use App\Models\Town;
use App\Models\User;
use App\Models\WholesalesUser;
use App\Services\Api\Checkout\ConfirmOrderService;
use App\Services\Order\CreateOrderProductService;
use App\Services\Order\CreateOrderService;
use App\Services\Order\CreateOrderTotalService;
use App\Services\User\AddressService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportOrderService
{

    private ConfirmOrderService $confirmOrderService;
    private CreateOrderService $createOrderService;
    private CreateOrderProductService  $createOrderProductService;
    private CreateOrderTotalService $createOrderTotalService;

    public static array $customerType = [
        'WHOLESALES' => WholesalesUser::class,
        'SUPERMARKET' => SupermarketUser::class
    ];


    /**
     * @param ConfirmOrderService $confirmOrderService
     * @param CreateOrderService $createOrderService
     * @param CreateOrderTotalService $createOrderTotalService
     * @param CreateOrderProductService $createOrderProductService
     */
    public function __construct(ConfirmOrderService $confirmOrderService, CreateOrderService $createOrderService, CreateOrderTotalService $createOrderTotalService, CreateOrderProductService  $createOrderProductService)
    {
        $this->confirmOrderService = $confirmOrderService;
        $this->createOrderService = $createOrderService;
        $this->createOrderProductService = $createOrderProductService;
        $this->createOrderTotalService = $createOrderTotalService;
    }


    /**
     * @param string $type
     * @param string $email
     * @return bool|SupermarketUser|WholesalesUser
     */
    public static function getUser(string $type, string $email) : bool | SupermarketUser | WholesalesUser
    {
        if(is_numeric($email)){
            $user = User::with(['wholesales_user', 'supermarket_user'])->where('phone', $email)->first();
        } else {
            $user =User::with(['wholesales_user', 'supermarket_user'])->where('email', $email)->first();
        }
        if(!$user) return false;

        if($type === WholesalesUser::class) {
            if(isset( $user->wholesales_user)) return  $user->wholesales_user;
        }

        if($type === SupermarketUser::class) {
            if(isset( $user->supermarket_user)) return  $user->supermarket_user;
        }

        return false;
    }


    /**
     * @param int $local_id
     * @return mixed
     */
    public static function getAddressId(int $local_id)
    {
        $address =  Address::where('local_address_id', $local_id)->first();
        if($address) return $address->id;

        dump('address with local_address_id not found '.$local_id);

        $localAddress = \App\Models\Old\Address::find($local_id);

        if(!$localAddress) {
            $address = request()->user()->address;
            if(!$address) dd('user does not have any address what can we now '.$local_id);
            return $address->id;
        }

        if(isset($localAddress->town_id)) {
            $town = Town::where('name', $localAddress?->town?->town_name)->first();
        }
        if(isset($address->zone_id)) {
            $state = State::where('name', str_replace(' State', '',$localAddress->zone->name))->first();
        }

        if(!isset($localAddress->address_1)) dd($localAddress);

        $data = [
            'name' => ($localAddress->firstname ?? "My"). ' '. ($localAddress->lastname ?? "Address"),
            'address_1' => $localAddress->address_1,
            'address_2' => $localAddress->address_2,
            'country_id' => config('app.DEFAULT_COUNTRY_ID'),
            'state_id' => $state?->id ?? NULL,
            'town_id'=> $town?->id ?? NULL,
            'local_address_id' => $localAddress->id
        ];

        $addressService = app(AddressService::class);
        $address = $addressService->createAddress(request()->user(), $data);
        return $address->id;
    }


    public static array $appId = [
        WholesalesUser::class => 5,
        SupermarketUser::class => 6,
    ];


    public static $orderStatus = [
        1 =>  18,
        2 => 15,
        3 => 19,
        4 => 5,
        5 => 10,
        6 => 16,
        7 => 20,
        8 => 7
    ];


    /**
     * @param array $contents
     * @return bool
     * @throws \Throwable
     */
    public function handle(array $contents) : bool
    {
        $customer = self::getUser(self::$customerType[$contents['store']], $contents['user']['email']);

        $telephone = normalizePhoneNumber($contents['user']['phone']);
        $telephone = str_replace("-", "", $contents['user']['phone']);
        $telephone = str_replace(" ", "", $contents['user']['phone']);

        if(!$customer) $customer = self::getUser(self::$customerType[$contents['store']], $telephone);

        if(!$customer) {
            dump('There is no customer with email '.$contents['email'].' or phone number '.$telephone);
            return false;
        }

        request()->setUserResolver(function () use ($customer) {
            return $customer->user;
        });
        auth('sanctum')->setUser($customer->user);
        ApplicationEnvironment::createApplicationEnvironment(App::findorfail(self::$appId[self::$customerType[$contents['store']]]));

        $sales_rep = NULL;
        if(!is_null($contents['sales_rep_id'])) {
            $sales_rep = SalesRepresentative::query()->where('old_server_id', $contents['sales_rep_id'])->first();
        }

        $orderData = [
            'local_order_id' => $contents['id'],
            'order_id' => generateUniqueid(12),
            'invoice_no' => $contents['invoice_no'],
            'customer_type' => self::$customerType[$contents['store']],
            'customer_id' => $customer?->id,
            'customer_group_id' => $customer?->customer_group_id,
            'firstname' => $contents['firstname'],
            'lastname' => $contents['lastname'],
            'email' => $contents['email'],
            'telephone' => $contents['telephone'],
            'order_date' => $contents['order_date'],
            'payment_method_id' => $contents['payment_method_id'],
            'delivery_method_id' => $contents['shipping_method_id'],
            'comment' => $contents['comment'],
            'total' => $contents['total'],
            'status_id' => self::$orderStatus[$contents['order_status_id']],
            'ip' => $contents['ip'],
            'user_agent' => $contents['user_agent'],
            'payment_address_id' => self::getAddressId($contents['payment_address_id']),
            'shipping_address_id' => self::getAddressId($contents['shipping_address_id']),
            'payment_gateway_response' => $contents['payment_gateway_response'] ?? NULL,
            'checkout_data' => $contents['checkout_data'] ?? NULL,
            'ordertotals' => $contents['ordertotals'] ?? NULL,
            'no_of_cartons' => $contents['no_of_cartons'] ?? NULL,
            'prove_of_payment' => NULL,
            'order_validation_error' => NULL,
            'app_id' => self::$appId[self::$customerType[$contents['store']]],
            'sales_representative_id' => $sales_rep ? $sales_rep?->id : NULL,
            'coupon_information' => NULL,
            'voucher_information' => NULL,
            'cart_cache' => NULL,
            'created_at' => carbonize($contents['created_at']),
            'updated_at' => carbonize($contents['updated_at'])
        ];
        return DB::transaction(function () use ($orderData, $contents){
            Order::where('invoice_no', $contents['invoice_no'])->delete();

            $order = $this->createOrderService->create($orderData);
            $order = $this->createOrderProductService->createOrderProductFromOldServer($order, $contents['order_products']);
            $this->createOrderTotalService->createOrderTotalFromOlderServer($order, $contents['order_total_orders']);
            return true;
        });

    }

}
