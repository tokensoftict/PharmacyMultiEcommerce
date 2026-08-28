<?php

use Illuminate\Support\Facades\Schedule;

//Schedule::command("app:send-push-notification-to-clients-phone")->everyMinute()->withoutOverlapping();
//Schedule::command("app:import-order-from-old-server")->everyTwoMinutes()->withoutOverlapping();
Schedule::command("app:rec-calculate-door-step-delivery")->everyMinute()->withoutOverlapping();

// Campaign: Check for abandoned carts every 15 minutes
Schedule::command("campaigns:check-cart-abandonment")->everyFifteenMinutes()->withoutOverlapping();

