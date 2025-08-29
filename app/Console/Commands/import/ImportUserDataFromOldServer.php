<?php

namespace App\Console\Commands\import;

use App\Models\Old\Address;
use App\Models\Old\RetailCustomer;
use App\Models\Old\SalesRepCustomerMapper;
use App\Models\Old\User;
use App\Models\SalesRepresentative;
use App\Models\State;
use App\Models\Town;
use App\Services\User\AddressService;
use App\Services\User\AppUserService;
use App\Services\User\Supermarket\SupermarketCustomerService;
use App\Services\User\UserAccountService;
use App\Services\User\Wholesales\WholeSalesCustomerService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ImportUserDataFromOldServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-user-data-from-old-server';

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
        ini_set('memory_limit', '1024M');
        $userAccountService = app(UserAccountService::class);
        $wholesalesCustomerService = app(WholeSalesCustomerService::class);
        $appUserService = app(AppUserService::class);
        $addressService = app(AddressService::class);
        $supermarketCustomerService = app(SupermarketCustomerService::class);

        $users = User::query()->with(['addresses', 'addresses.town', 'addresses.zone']);
        $users->chunk(2, function ($users) use($userAccountService, $wholesalesCustomerService, $appUserService, $addressService, $supermarketCustomerService){
            $this->insertWholesaleUser($users, $userAccountService, $wholesalesCustomerService, $appUserService, $addressService, $supermarketCustomerService);
        });

        $retailUsers = RetailCustomer::query()->with(['addresses', 'addresses.town', 'addresses.zone']);
        $retailUsers->chunk(2, function ($users) use($userAccountService, $wholesalesCustomerService, $appUserService, $addressService, $supermarketCustomerService){
            $this->insertSuperMarketUser($users, $userAccountService, $wholesalesCustomerService, $appUserService, $addressService, $supermarketCustomerService);
        });

        $this->mapSalesRepresentativeWithCustomer();

    }
    private function insertWholesaleUser($users, $userAccountService, $wholesalesCustomerService, $appUserService, $addressService, $supermarketCustomerService) {
        DB::transaction(function() use ($users, $userAccountService, $wholesalesCustomerService, $appUserService, $addressService, $supermarketCustomerService){
            foreach ($users as $user) {
                $userData = [
                    "email" => $user->email,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "phone" => $user->phone,
                    "password" => $user->password
                ];
                $wholeSalesData = [
                    'customer_group_id' => NULL,
                    'customer_type_id' => $user->type,
                    'business_name' => $user->shop_name,
                    'cac_document' => NULL,
                    'premises_licence' => NULL,
                    'phone' => $user->phone,
                    'address_id' => NULL
                ];

                $userAccount = $userAccountService->createUserAccount($userData);
                $userAccountService->activateUserAccount($userAccount);
                // let's create a supermarket account for the user since supermarket does not need
                // any administrator validation
                $superMarketUser = $supermarketCustomerService->createCustomer(['phone' => $user->phone], $userAccount);
                $appUserService->createAppUser($userAccount, $superMarketUser);

                $wholesale = $wholesalesCustomerService->createCustomer($wholeSalesData, $userAccount);
                $appUserService->createAppUser($userAccount, $wholesale);
                $wholesalesCustomerService->activateBusiness($wholesale);

                $formatAddresses = $this->formatAddress(Address::where('user_type', 'App\\User')->where('user_id', $user->id)->get());
                foreach ($formatAddresses as $formatAddress) {
                    $addressService->createAddress($userAccount, $formatAddress);
                }
                $userAccount->updateDeviceKey($user->device_key);

                if($user->isSalesRep()) {
                    $salesRepData = [
                        'status' => "1",
                        'user_id' => $userAccount->id,
                        'invitation_status' => "1",
                        "invitation_sent_date" => NULL,
                        'invitation_approval_date'=> now(),
                        'added_by' => \App\Models\User::selfSystem()->id,
                        'token' => NULL,
                        'old_server_id' => $user->id
                    ];
                    app(UserAccountService::class)->createSalesRepAccount($userAccount,$salesRepData , false);
                }

                if($user->isAdmin()) {
                    $adminData = [
                        'status' => "1",
                        'user_id' => $userAccount->id,
                        'invitation_status' => "1",
                        "invitation_sent_date" => now(),
                        'added_by' =>  \App\Models\User::selfSystem()->id,
                    ];
                    app(UserAccountService::class)->createWholesalesAdministrator($userAccount, $adminData, false);
                    app(UserAccountService::class)->createSuperMarketAdministrator($userAccount, $adminData, false);

                    $userAccount->assignRole(config('app.SUPER_ADMINISTRATOR'));
                }

            }
        });
    }

    private function insertSuperMarketUser($users, $userAccountService, $wholesalesCustomerService, $appUserService, $addressService, $supermarketCustomerService) {
        DB::transaction(function() use ($users, $userAccountService, $wholesalesCustomerService, $appUserService, $addressService, $supermarketCustomerService){
            foreach ($users as $user) {
                $userData = [
                    "email" => $user->email,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "phone" => $user->phone,
                    "password" => $user->password
                ];

                $userAccount = $userAccountService->createUserAccount($userData);
                $userAccountService->activateUserAccount($userAccount);
                // let's create a supermarket account for the user since supermarket does not need
                // any administrator validation
                $superMarketUser = $supermarketCustomerService->createCustomer(['phone' => $user->phone], $userAccount);
                $appUserService->createAppUser($userAccount, $superMarketUser);

                $formatAddresses = $this->formatAddress(Address::where('user_type', 'App\\RetailCustomer')->where('user_id', $user->id)->get());
                foreach ($formatAddresses as $formatAddress) {
                    $addressService->createAddress($userAccount, $formatAddress);
                }

                $userAccount->updateDeviceKey($user->device_key);
            }
        });
    }

    /**
     * @param Collection $addresses
     * @return array
     */
    private function formatAddress(Collection $addresses)
    {
        $data = [];
        foreach ($addresses as $address) {
            $town = NULL;
            $state = NULL;

            if(isset($address->town_id)) {
                $town = Town::where('name', $address?->town?->town_name)->first();
            }
            if(isset($address->zone_id)) {
                $state = State::where('name', str_replace(' State', '',$address->zone->name))->first();
            }

            $data[] = [
                'name' => $address->firstname. ' '. $address->lastname,
                'address_1' => $address->address_1,
                'address_2' => $address->address_1,
                'country_id' => config('app.DEFAULT_COUNTRY_ID'),
                'state_id' => $state?->id ?? NULL,
                'town_id'=> $town?->id ?? NULL,
                'local_address_id' => $address->id
            ];

        }

        return $data;
    }

    private function mapSalesRepresentativeWithCustomer() : void
    {
        $salesRepresentatives = SalesRepresentative::all();
        foreach ($salesRepresentatives as $salesRepresentative) {
            $myCustomers = SalesRepCustomerMapper::with(['customer'])->where('user_id', $salesRepresentative->old_server_id)->get();
            foreach ($myCustomers as $customer) {
                $newCustomer = \App\Models\User::query()->with('wholesales_user')->where('email', $customer->customer->email)->first();
                if(!$newCustomer) {
                    dump($customer->customer->email." not found");
                    continue;
                }

                $newCustomer->wholesales_user->sales_representative_id = $salesRepresentative->id;
                $newCustomer->wholesales_user->save();
            }
        }
    }
}
