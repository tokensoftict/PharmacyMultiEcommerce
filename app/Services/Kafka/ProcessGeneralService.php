<?php

namespace App\Services\Kafka;

use App\Enums\KafkaAction;
use App\Enums\PushNotificationAction;
use App\Models\Classification;
use App\Models\LocalCustomer;
use App\Models\Manufacturer;
use App\Models\MemberGroup;
use App\Models\NewStockArrival;
use App\Models\Productcategory;
use App\Models\Productgroup;
use App\Models\Stock;
use App\Models\User;
use App\Services\Utilities\PushNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Junges\Kafka\Message\ConsumedMessage;

class ProcessGeneralService
{
    public static function handle(ConsumedMessage $message): void
    {
        $body = $message->getBody();
        $action = $body[0]['KAFKA_ACTION'];
        $data = $body[0]['data'];
        Log::info($action);
        switch ($action) {
            case KafkaAction::CREATE_BRAND:
                self::createBrand($data);
                break;
            case KafkaAction::UPDATE_BRAND:
                self::updateBrand($data);
                break;
            case KafkaAction::CREATE_CATEGORY:
                self::createCategory($data);
                break;
            case KafkaAction::UPDATE_CATEGORY:
                self::updateCategory($data);
                break;
            case KafkaAction::CREATE_CLASSIFICATION:
                self::createClassification($data);
                break;
            case KafkaAction::UPDATE_CLASSIFICATION:
                self::updateClassification($data);
                break;
            case KafkaAction::CREATE_CUSTOMER:
                self::createCustomer($data);
                break;
            case KafkaAction::UPDATE_CUSTOMER:
                self::updateCustomer($data);
                break;
            case KafkaAction::CREATE_MANUFACTURER:
                self::createManufacturer($data);
                break;
            case KafkaAction::UPDATE_MANUFACTURER:
                self::updateManufacturer($data);
                break;
            case KafkaAction::NEW_ARRIVAL:
                self::newArrival($data, $body[0]['store']);
                break;
            case KafkaAction::CREATE_STOCK_GROUP:
                self::createStockGroup($data);
                break;
            case KafkaAction::UPDATE_STOCK_GROUP:
                self::updateStockGroup($data);
                break;
            case KafkaAction::EARNED_LOYALTY:
                self::updateUserLoyalty($data);
                break;
            case KafkaAction::CREATE_MEMBER_GROUP:
                self::createMemberGroup($data);
                break;
            case KafkaAction::UPDATE_MEMBER_GROUP:
                self::updateMemberGroup($data);
                break;
        }

    }


    public static function createBrand(array $data): void
    {

    }

    public static function updateBrand(array $data): void
    {

    }

    /**
     * @param array $data
     * @return Productcategory|bool
     */
    public static function createCategory(array $data): Productcategory|bool
    {
        if (isset($data[1])) {
            Schema::disableForeignKeyConstraints();
            DB::table("productcategories")->truncate();
            $result = DB::table("productcategories")->insert($data);
            Schema::enableForeignKeyConstraints();
            return $result;
        }
        else {
            return Productcategory::create($data);
        }

    }

    /**
     * @param array $data
     * @return Productcategory
     */
    public static function updateCategory(array $data): Productcategory
    {
        return Productcategory::where("id", $data['id'])->update($data);
    }

    /**
     * @param array $data
     * @return Classification|bool
     */
    public static function createClassification(array $data): Classification|bool
    {
        if (isset($data[1])) {
            Schema::disableForeignKeyConstraints();
            DB::table("classifications")->truncate();
            $result = DB::table("classifications")->insert($data);
            Schema::enableForeignKeyConstraints();
            return $result;
        }
        else {
            return Classification::create($data);
        }

    }

    /**
     * @param array $data
     * @return Classification|bool
     */
    public static function updateClassification(array $data): Classification|bool
    {
        Schema::disableForeignKeyConstraints();
        DB::table("classifications")->truncate();
        $result = Classification::where("id", $data['id'])->update($data);
        Schema::enableForeignKeyConstraints();
        return $result;
    }

    /**
     * @param array $data
     * @return LocalCustomer|bool
     */
    public static function createCustomer(array $data): LocalCustomer|bool
    {
        if (isset($data[1])) {
            $result = DB::table("local_customers")->insert($data);
            foreach ($data as $customer) {
                self::updateUserLocalId($customer);
            }
            return $result;
        }
        else {
            $customer = LocalCustomer::create($data);
            self::updateUserLocalId($data);
            return $customer;
        }
    }

    /**
     * @param array $data
     * @return LocalCustomer|bool
     */
    public static function updateCustomer(array $data): LocalCustomer|bool
    {
        if (isset($data[1])) {
            foreach ($data as $customer) {
                $localCustomer = LocalCustomer::where('local_id', $customer['local_id'])->first();
                if (!$localCustomer) {
                    self::updateUserLocalId($customer);
                    return self::createCustomer($customer);
                }
                self::updateUserLocalId($customer);
                $localCustomer->update($customer);
            }
            return true;
        }
        else {
            $localCustomer = LocalCustomer::where('local_id', $data['local_id'])->first();
            if (!$localCustomer) {
                self::updateUserLocalId($data);
                return self::createCustomer($data);
            }

            self::updateUserLocalId($data);
            return $localCustomer->update($data);
        }

    }

    /**
     * @param array $data
     * @return Manufacturer|bool
     */
    public static function createManufacturer(array $data): Manufacturer|bool
    {
        if (isset($data[1])) {
            Schema::disableForeignKeyConstraints();
            DB::table("manufacturers")->truncate();
            $result = DB::table("manufacturers")->insert($data);
            Schema::enableForeignKeyConstraints();
            return $result;
        }
        else {
            return Manufacturer::create($data);
        }

    }

    /**
     * @param array $data
     * @return Manufacturer
     */
    public static function updateManufacturer(array $data): Manufacturer|bool
    {
        return Manufacturer::where("id", $data['id'])->update($data);
    }


    /**
     * @param array $data
     * @param string $store
     * @return bool
     */
    public static function newArrival(array $data, string $store): bool
    {
        $localStockIDs = array_keys($data);
        $localStock = $data;
        $stocks = Stock::whereIn("local_stock_id", $localStockIDs)->get();
        $newArrivalStocks = $stocks->map(function ($stock) use ($store, $localStock) {
            return [
            "stock_id" => $stock->id,
            "app_id" => $store,
            "quantity" => $localStock[$stock->local_stock_id]['qty'],
            "arrival_date" => date("Y-m-d")
            ];
        })->toArray();

        foreach ($newArrivalStocks as $newArrivalStock) {
            NewStockArrival::create($newArrivalStock);
        }

        return true;
    }


    /**
     * @param array $data
     * @return Productgroup|bool
     */
    public static function createStockGroup(array $data): Productgroup|bool
    {
        if (isset($data[1])) {
            Schema::disableForeignKeyConstraints();
            DB::table("productgroups")->truncate();
            $result = DB::table("productgroups")->insert($data);
            Schema::enableForeignKeyConstraints();
            return $result;
        }
        else {
            return Productgroup::create($data);
        }
    }


    /**
     * @param array $data
     * @return Productgroup
     */
    public static function updateStockGroup(array $data): Productgroup|bool
    {
        return Productgroup::where("id", $data['id'])->update($data);
    }


    public static function updateUserLocalId($data): bool
    {
        $user = User::where('phone', $data['phone_number'])->first();
        if ($user) {
            $oldPoints = $user->loyalty_points;
            $oldGroupId = $user->member_group_id;
            $user->update([
                'local_id' => $data['local_id'],
                'loyalty_points' => $data['loyalty_points'],
                'member_group_id' => $data['member_group_id']
            ]);

            if ($data['loyalty_points'] > $oldPoints) {
                self::sendLoyaltyNotification($user, $data['loyalty_points']);
            }

            if ($data['member_group_id'] != $oldGroupId && !is_null($data['member_group_id'])) {
                self::sendMemberGroupNotification($user, $data['member_group_id']);
            }
            return true;
        }
        return false;
    }



    public static function updateUserLoyalty($data): bool
    {
        $user = User::where('local_id', $data['local_id'])->first();
        if ($user) {
            $oldPoints = $user->loyalty_points;
            $oldGroupId = $user->member_group_id;
            $user->update([
                'loyalty_points' => $data['loyalty_points'],
                'member_group_id' => $data['member_group_id']
            ]);

            if ($data['loyalty_points'] > $oldPoints) {
                self::sendLoyaltyNotification($user, $data['loyalty_points']);
            }

            if ($data['member_group_id'] != $oldGroupId && !is_null($data['member_group_id'])) {
                self::sendMemberGroupNotification($user, $data['member_group_id']);
            }

            return true;
        }
        return false;
    }


    public static function createMemberGroup(array $data): MemberGroup|bool
    {
        if (isset($data[1])) {
            Schema::disableForeignKeyConstraints();
            DB::table("member_groups")->truncate();
            $result = DB::table("member_groups")->insert($data);
            Schema::enableForeignKeyConstraints();
            return $result;
        }
        else {
            return MemberGroup::create($data);
        }
    }

    public static function updateMemberGroup(array $data): MemberGroup|bool
    {
        return MemberGroup::where("id", $data['id'])->update($data);
    }

    /**
     * @param User $user
     * @param float $newPoints
     * @return void
     */
    private static function sendLoyaltyNotification(User $user, float $newPoints): void
    {
        $notifications = [
            ["title" => "Points Alert! 🎊", "body" => "You've just earned more loyalty points! Your total is now $newPoints. Keep it up!"],
            ["title" => "Score Update! 📈", "body" => "Your loyalty points just went up! You now have $newPoints points to spend."],
            ["title" => "You've Got Points! ✨", "body" => "Congratulations! Your loyalty points have increased to $newPoints. Check them out in the app."],
            ["title" => "Loyalty Level Up! 🚀", "body" => "You're earning points fast! Your balance is now $newPoints. Thanks for shopping with us!"],
            ["title" => "Points Earned! 🏆", "body" => "Nicely done! Your loyalty point balance just hit $newPoints. See what rewards await you!"]
        ];

        $randomNotification = $notifications[array_rand($notifications)];

        $notificationService = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment(6) // Supermarket
            ->setUserCustomer($user)
            ->createNotification([
            "title" => $randomNotification['title'],
            "body" => $randomNotification['body'],
        ])
            ->setAction(PushNotificationAction::NONE)
            ->approve()
            ->send();
    }

    /**
     * @param User $user
     * @param int $newGroupId
     * @return void
     */
    private static function sendMemberGroupNotification(User $user, int $newGroupId): void
    {
        $group = MemberGroup::find($newGroupId);
        if (!$group) return;

        $label = $group->label;

        $notifications = [
            ["title" => "Status Upgrade! 🎊", "body" => "You've been promoted to $label! Enjoy your new exclusive perks and rewards."],
            ["title" => "New Tier Unlocked! 💎", "body" => "Congratulations! You are now a $label member. Check the app to see what's new."],
            ["title" => "Welcome to $label! 🌟", "body" => "Your continued patronage has earned you a spot in our $label group. Well done!"],
            ["title" => "You've Leveled Up! 🚀", "body" => "Thanks for being an amazing customer! You've been upgraded to $label status."],
            ["title" => "Exclusive Access! 🏆", "body" => "Your account has been upgraded to $label. Big things are coming your way!"]
        ];

        $randomNotification = $notifications[array_rand($notifications)];

        $notificationService = new PushNotificationService();
        $notificationService
            ->setApplicationEnvironment(6) // Supermarket
            ->setUserCustomer($user)
            ->createNotification([
                "title" => $randomNotification['title'],
                "body" => $randomNotification['body'],
            ])
            ->setAction(PushNotificationAction::NONE)
            ->approve()
            ->send();
    }

}