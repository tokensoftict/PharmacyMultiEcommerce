<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use function Laravel\Prompts\confirm;

class ServerPushStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:server-push-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $status = Cache::get("server-push-status", function (){
            return true;
        });

        $message = match ($status){
            true => "Do you want bring down push server ?",
            default => "Do you want bring up push server ?",
        };


        $confirmed = confirm(
            label :$message,
            default: false,
            yes: "Yes",
            no : "No",
        );

        if($confirmed === true){
            Cache::rememberForever("server-push-status", !$status);
        }

    }
}
