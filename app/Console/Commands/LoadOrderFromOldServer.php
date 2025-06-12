<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesUser;
use App\Services\Api\Checkout\ConfirmOrderService;
use App\Services\ImportOrderService;
use App\Services\Order\CreateOrderProductService;
use App\Services\Order\CreateOrderService;
use App\Services\Order\CreateOrderTotalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LoadOrderFromOldServer extends Command
{

    protected ConfirmOrderService $confirmOrderService;
    protected CreateOrderService $createOrderService;
    protected CreateOrderProductService  $createOrderProductService;
    protected CreateOrderTotalService $createOrderTotalService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-order-from-old-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is load order from old server, when this app goes life some customer will still be ordering from the old app, we want add there order  to the new app';

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
        parent::__construct();
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
        $user = User::with(['wholesales_user', 'supermarket_user'])->where('email', $email)->first();
        if(!$user) return false;

        if($type === WholesalesUser::class) {
            if(isset( $user->wholesales_user)) return  $user->wholesales_user;
        }

        if($type === SupermarketUser::class) {
            if(isset( $user->supermarket_user)) return  $user->supermarket_user;
        }

        return false;
    }


    public static array $appId = [
        WholesalesUser::class => 5,
        SupermarketUser::class => 6,
    ];

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle()
    {
        $contents = Storage::json('sample-older-server-order.json');
        //_FETCH(config('app.PROCESS_OLD_APP_URL').'unprocessedorder');

        $this->info('Checking for new Order from '.config('app.PROCESS_OLD_APP_URL'));
        if(is_array($contents)){
            if(count($contents) == 0) {
                $this->info('No Pending order to process');
                return Command::SUCCESS;
            }
        }

        $processOrder = app(ImportOrderService::class)->handle($contents);
        if($processOrder) {
            //_GET(config('app.PROCESS_OLD_APP_URL').'processorder/'.$contents['id']."/2");
        }

        return Command::SUCCESS;
    }
}
