<?php

namespace App\Services\Utilities;

use App\Models\App;
use App\Models\PushNotification;
use App\Models\PushNotificationCustomer;
use App\Models\PushNotificationStock;
use App\Models\Stock;
use App\Models\SupermarketsStockPrice;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesUser;
use App\Models\WholessalesStockPrice;
use Illuminate\Support\Arr;

class PushNotificationService
{
    /**
     * @var PushNotification
     */
    private PushNotification $pushNotification;
    private ?App $app = NULL;


    public function __construct(PushNotification|null $pushNotification =  null)
    {
        if(!is_null($pushNotification)){
            $this->pushNotification = $pushNotification;
        }
    }


    /**
     * @param App|int $app
     * @return $this
     */
    public final function setApplicationEnvironment(App | int $app) : self
    {
        if(!$app instanceof App){
            $app = App::find($app);
        }

        $this->app = $app;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public final function createNotification(array $data) : self
    {
        $data = Arr::only($data, [
            "title",
            "body",
            "payload",
            "device_ids",
            "app_id",
            "action",
            "type",
            "status",
        ]);

        $data['user_id'] = request()?->user()?->id ?? User::selfSystem()->id;
        $this->pushNotification = PushNotification::create($data);
        $this->pushNotification =  $this->pushNotification->fresh();
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public final function update(array $data) : self
    {
        $data = Arr::only($data, [
            "title",
            "body",
            "payload",
            "device_ids",
            "app_id",
            "action",
            "type",
            "status",
        ]);

        if($this->app !== NULL){
            $data['app_id'] = $this->app->id;
        }

        $this->pushNotification->update($data);
        $this->pushNotification =  $this->pushNotification->fresh();
        return $this;
    }

    /**
     * @return $this
     */
    public final function approve() : self
    {
        $this->pushNotification->status = "APPROVED";
        $this->pushNotification->update();
        $this->pushNotification =  $this->pushNotification->fresh();
        return $this;
    }


    /**
     * @return PushNotificationService
     */
    public final function cancel() : self
    {
        $this->pushNotification->status = "CANCEL";
        $this->pushNotification->update();
        $this->pushNotification =  $this->pushNotification->fresh();
        return $this;
    }


    /**
     * @return $this]
     */
    public final function send() : self
    {
        if($this->pushNotification->status === "APPROVED"){
            sendNotificationToDevice($this->pushNotification);
        }
       return $this;
    }


    /**
     * @param WholesalesUser|int $user
     * @return PushNotificationService
     */
    public final function setWholesaleCustomer(WholesalesUser | int $user) : self
    {
        if(!$user instanceof WholesalesUser){
            $user = WholesalesUser::find($user);
        }

        $this->pushNotification->push_notification_customers()->save(new PushNotificationCustomer([
            'customer_type' => WholesalesUser::class,
            'customer_id' => $user->id,
            'status_id' => status('Pending'),
        ]));
        $this->pushNotification =  $this->pushNotification->fresh();
        return $this;
    }

    /**
     * @param SupermarketUser|int $user
     * @return PushNotificationService
     */
    public final function setSuperMarketCustomer(SupermarketUser | int $user) : self
    {
        if(!$user instanceof SupermarketUser){
            $user = SupermarketUser::find($user);
        }

        $this->pushNotification->push_notification_customers()->save(new PushNotificationCustomer([
            'customer_type' => SupermarketUser::class,
            'customer_id' => $user->id,
            'status_id' => status('Pending'),
        ]));
        $this->pushNotification =  $this->pushNotification->fresh();
        return $this;
    }

    /**
     * @param User|int $user
     * @return PushNotificationService
     */
    public final function setUserCustomer(User | int $user) : self
    {
        if(!$user instanceof User){
            $user = User::find($user);
        }

        $superMarketUser = SupermarketUser::where("user_id", $user->id)->first();

        if($superMarketUser){
            return $this->setSuperMarketCustomer($superMarketUser);
        }

        return $this;
    }

    /**
     * @param Stock|int $stock
     * @return $this
     */
    public final function setProduct(Stock | int $stock) : self
    {
        if(!$stock instanceof Stock){
            $stock = User::find($stock);
        }

        $app =  $this?->app ?? $this->pushNotification->app;
        $stock_model = match ($app->model_id){
            6 => SupermarketsStockPrice::class,
            default=>WholessalesStockPrice::class,
        };


        $this->pushNotification->stocks()->save(new PushNotificationStock([
            'stock_id' => $stock->id,
            'app_id' =>$stock_model
        ]));

        $this->pushNotification =  $this->pushNotification->fresh();
        return $this;
    }


    /**
     * @param array $payload
     * @return $this
     */
    public final function setPayload(array $payload) : self
    {
        $this->pushNotification->payload = $payload;
        $this->pushNotification->update();
        return $this;

    }

    /**
     * @param string $action
     * @return $this
     */
    public final function setAction(string $action) : self
    {
        $this->pushNotification->action = $action;
        $this->pushNotification->update();
        return $this;

    }
}
