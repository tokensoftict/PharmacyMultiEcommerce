<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command("app:send-push-notification-to-clients-phone")->everyMinute()->withoutOverlapping();
Schedule::command("app:import-order-from-old-server")->everyTwoMinutes()->withoutOverlapping();
