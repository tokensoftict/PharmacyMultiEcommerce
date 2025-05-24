<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command("queue:work --stop-when-empty")->everyMinute()->withoutOverlapping();
Schedule::command("app:send-push-notification-to-clients-phone")->everyMinute()->withoutOverlapping();
