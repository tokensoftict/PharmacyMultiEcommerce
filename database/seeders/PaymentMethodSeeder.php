<?php

namespace Database\Seeders;

use App\Classes\AppLists;
use App\Models\WholesalesUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            'WHOLESALES' => 5, //config('app.WHOLESALES_DOMAIN'),
            'SUPERMARKET' => 6 //config('app.SUPERMARKET_DOMAIN')
        ];

        $payment_methods = json_decode('[{"id":"1","name":"Bank Transfer","description":"Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be processed until the funds have cleared in our account.","path":"BankTransfer","code":"Bank","status":"1","store":"WHOLESALES","template_settings":null,"template_settings_value":"[{\"bank\": \"PS GENERAL DRUGS\", \"name\": \"GTBank\", \"number\": \"0736145025\"}]","checkout_template":null,"created_at":"2021-05-11 08:16:22","updated_at":"2023-02-16 20:17:06"},{"id":"2","name":"Pay At Store","description":"Make your payment when picking up at our \r\nyour item(s) at our store.","path":"Pat","code":"Pat","status":"1","store":"WHOLESALES","template_settings":null,"template_settings_value":"{\"address\": \"No 18 Unit Oke Odo Opposite Ilorin International Airport, Ilorin Kwara State\"}","checkout_template":null,"created_at":"2021-05-11 08:16:22","updated_at":"2021-05-29 14:55:43"},{"id":"3","name":"Flutterwave Payment Gateway","description":"Flutterwave provides the easiest and most reliable payments solution for businesses anywhere in the world.","path":"Flutterwave","code":"Flutterwave","status":"0","store":"WHOLESALES","template_settings":null,"template_settings_value":null,"checkout_template":null,"created_at":"2021-05-11 08:16:22","updated_at":"2021-06-03 21:27:44"},{"id":"4","name":"Paystack Payment Gateway","description":"Paystack is a technology company solving payments problems for ambitious businesses. Our mission is to help businesses in Africa become profitable, envied, and loved.","path":"Paystack","code":"Paystack","status":"1","store":"WHOLESALES","template_settings":null,"template_settings_value":"{\"pub_key\": \"pk_live_f60086e7f71734ab9f8d68ef7b13143521a1a9e3\", \"sec_key\": \"sk_live_1ef6f9901b5d2b87337269ba580944ec80bd7da5\", \"environment\": \"production\"}","checkout_template":null,"created_at":"2021-05-11 08:16:22","updated_at":"2021-09-07 23:42:16"},{"id":"5","name":"Bank Transfer","description":"Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be processed until the funds have cleared in our account.","path":"BankTransfer","code":"Bank","status":"1","store":"SUPERMARKET","template_settings":null,"template_settings_value":"[{\"bank\": \"PS GENERAL DRUGS\", \"name\": \"STERLING BANK\", \"number\": \"0079729045\"}]","checkout_template":null,"created_at":"2021-05-11 08:16:22","updated_at":"2022-04-08 21:40:00"},{"id":"6","name":"Pay At Store","description":"Make your payment when picking up at our \r\nyour item(s) at our store.","path":"Pat","code":"Pat","status":"1","store":"SUPERMARKET","template_settings":null,"template_settings_value":"{\"address\": \"No 18 Unit Oke Odo Opposite Ilorin International Airport, Ilorin Kwara State\"}","checkout_template":null,"created_at":"2021-05-11 08:16:22","updated_at":"2021-05-29 14:55:43"},{"id":"7","name":"Flutterwave Payment Gateway","description":"Flutterwave provides the easiest and most reliable payments solution for businesses anywhere in the world.","path":"Flutterwave","code":"Flutterwave","status":"0","store":"SUPERMARKET","template_settings":null,"template_settings_value":null,"checkout_template":null,"created_at":"2021-05-11 08:16:22","updated_at":"2021-06-03 21:27:44"},{"id":"8","name":"Paystack Payment Gateway","description":"Paystack is a technology company solving payments problems for ambitious businesses. Our mission is to help businesses in Africa become profitable, envied, and loved.","path":"Paystack","code":"Paystack","status":"1","store":"SUPERMARKET","template_settings":null,"template_settings_value":"{\"pub_key\": \"pk_live_f60086e7f71734ab9f8d68ef7b13143521a1a9e3\", \"sec_key\": \"sk_live_1ef6f9901b5d2b87337269ba580944ec80bd7da5\", \"environment\": \"production\"}","checkout_template":null,"created_at":"2021-05-11 08:16:22","updated_at":"2021-09-07 23:42:16"}]', true);
        foreach ($payment_methods as $key=>$payment){
            $payment_methods[$key]['app_id'] = $stores[$payment_methods[$key]['store']];
            unset($payment_methods[$key]['store'], $payment_methods[$key]['id'], $payment_methods[$key]['created_at'], $payment_methods[$key]['updated_at']);
        }
        DB::table('payment_methods')->insert($payment_methods);
    }
}
