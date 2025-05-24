<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command("queue:work --stop-when-empty")->everyFiveSeconds()->withoutOverlapping();
//Schedule::command("app:send-push-notification-to-clients-phone")->everyFiveSeconds()->withoutOverlapping();