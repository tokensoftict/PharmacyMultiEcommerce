<?php

namespace App\Jobs;

use App\Classes\ApplicationEnvironment;
use App\Classes\Settings;
use App\Enums\PushNotificationAction;
use App\Http\Resources\Api\Stock\StockShowResource;
use App\Models\App;
use App\Models\MedReminder;
use App\Models\UserStockPromotion;
use App\Services\Utilities\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TriggerGenerateMedReminderDiscount implements ShouldQueue
{
    use Queueable;
    public MedReminder $medReminder;
    /**
     * Create a new job instance.
     */
    public function __construct(MedReminder $medReminder)
    {
        $this->medReminder = $medReminder;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->medReminder->is_discount_generated)
            return;

        $settings = app(Settings::class);
        $discount = $settings->get('discount_percentage');
        $validity = (int) $settings->get('validity', 5);
        $stock = $this->medReminder->stock;
        $price = $stock->supermarkets_stock_prices->price;
        $discountAmount = $price - ceil(($discount / 100) * $price);

        $fromDate = Carbon::now()->format('Y-m-d');
        $toDate = Carbon::now()->addDays($validity)->format('Y-m-d');

        UserStockPromotion::create([
            'price' => $discountAmount,
            'discount_percentage' => (int) $discount,
            'user_id' => $this->medReminder->user_id,
            'stock_id' => $this->medReminder->stock_id,
            'app_id' => 6,
            'from_date' => $fromDate,
            'end_date' => $toDate,
            'created' => now()->toDateTimeString(),
            'status_id' => status('Approved')
        ]);

        $this->medReminder->update([
            'discount_percentage' => (int) $discount,
            'discount_generated_date' => now()->toDateTimeString(),
            'discount_expiry_date' => $toDate,
            'is_discount_generated' => true
        ]);


        $notifications = [
            [
                'title' => '🎁 You’ve Got a Discount!',
                'body' => 'Your medication is running low, and we’ve got a special discount just for you! 💊💸'
            ],
            [
                'title' => '⚠️ Discount Alert!',
                'body' => 'Heads up! You’ve earned a discount on your next refill. Tap to claim it now. 🛍️'
            ],
            [
                'title' => '⏰ It’s Discount Time!',
                'body' => 'Your refill is due soon, and your discount is waiting. Don’t miss out! 💰'
            ],
            [
                'title' => '🎉 Special Offer Inside!',
                'body' => 'We noticed your meds are almost out. Enjoy a discount on your next order! 🏷️💊'
            ],
            [
                'title' => '🙌 You’re Eligible!',
                'body' => 'You’ve reached your refill point! Claim your exclusive discount now. 🎯'
            ],
            [
                'title' => '🤑 Discount Unlocked!',
                'body' => 'A little gift from us to you — a discount is ready for your next med refill. 🎁'
            ],
            [
                'title' => '💸 Save on Your Next Refill!',
                'body' => 'We’ve prepared a special discount just for you. Grab it before it’s gone! ⌛'
            ],
            [
                'title' => '✨ Refill & Save!',
                'body' => 'Your prescription is due. Tap now to refill with a sweet discount! 📦💊'
            ],
            [
                'title' => '🔥 Hot Offer!',
                'body' => 'Act fast! You’ve earned a discount on your next medication order. ⏳💰'
            ],
            [
                'title' => '🌟 You Deserve This!',
                'body' => 'A discount has been added to your account. Enjoy some savings today! 😊'
            ],
            [
                'title' => '📢 Limited Time Discount!',
                'body' => 'Your meds are nearly finished. Refill now and enjoy a limited-time discount! ⏰'
            ],
            [
                'title' => '💊 Refill Reminder + Bonus!',
                'body' => 'Time to refill! And yes — there’s a discount waiting for you. 🎁🩺'
            ],
            [
                'title' => '🎈 Discount Day!',
                'body' => 'Today’s a good day to refill — and save! Tap to apply your discount. 💳'
            ],
            [
                'title' => '🎯 Bonus Reward Unlocked!',
                'body' => 'Thanks for staying on track! Your reward: a discount on your next order. 🏆'
            ],
            [
                'title' => '💖 We Care About You!',
                'body' => 'To support your health journey, we’ve gifted you a refill discount. 🎁'
            ],
        ];

        $randomNotification = $notifications[array_rand($notifications)];
        $randomNotification['app_id'] = 6;

        request()->setUserResolver(function () {
            return $this->medReminder->user;
        });

        auth('sanctum')->setUser($this->medReminder->user);
        ApplicationEnvironment::createApplicationEnvironment(App::find(6));

        $data = (new StockShowResource($this->medReminder->stock))->toArray(request());

        //generate notification for user
        $pushNotificationService = app(PushNotificationService::class);
        $pushNotificationService
            ->setApplicationEnvironment(6)
            ->createNotification($randomNotification)
            ->setAction(PushNotificationAction::VIEW_PRODUCT)
            ->setPayload($data)
            ->setUserCustomer($this->medReminder->user_id)
            ->approve()
            ->send();
    }
}
