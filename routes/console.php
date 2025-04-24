<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command("queue:work --stop-when-empty")->everyFiveSeconds()->withoutOverlapping();
