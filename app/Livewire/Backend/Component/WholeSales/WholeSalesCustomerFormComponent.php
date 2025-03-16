<?php

namespace App\Livewire\Backend\Component\WholeSales;

use App\Services\User\AddressService;
use App\Services\User\AppUserService;
use App\Services\User\UserAccountService;
use App\Services\User\Wholesales\WholeSalesCustomerService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class WholeSalesCustomerFormComponent extends Component
{
    public array $state_id;
    public array $town_id;
    public array $country_id;
    public array $formData;

    public array $data;

    public array $customer_type_id;
    public array $customer_group_id;

    public String $action = "New";

    private WholeSalesCustomerService $wholeSalesCustomerService;
    private UserAccountService $userAccountService;

    private AddressService $addressService;
    private AppUserService $appUserService;


    public function boot(WholeSalesCustomerService $wholeSalesCustomerService, UserAccountService $userAccountService, AddressService $addressService, AppUserService $appUserService)
    {
        $this->wholeSalesCustomerService = $wholeSalesCustomerService;
        $this->userAccountService = $userAccountService;
        $this->addressService = $addressService;
        $this->appUserService = $appUserService;
    }

    public function mount()
    {
        $this->formData['user'] = [
            'first_name',
            'last_name',
            'email',
            'password'
        ];

        $this->formData['wholesale'] = [
            'business_name',
            'business_cac_certificate',
            'business_premises_license',
            'customer_type_id',
            'customer_group_id',
            'phone',
            'business_email_address',
            'business_address_1',
            'business_address_2',
            'state_id' => "",
            'town_id' => ""
        ];

        $this->state_id =

        $this->customer_type_id = customerTypes()->toArray();

        $this->customer_group_id = customerGroups()->toArray();

        $this->town_id = statesByCountry(config('app.DEFAULT_COUNTRY_ID'))->toArray();

        $this->data['state_id']['options'] = statesByCountry(config('app.DEFAULT_COUNTRY_ID'))->values()->toArray();
        $this->data['customer_type_id']['options'] = customerTypes()->toArray();
        $this->data['customer_group_id']['options'] = customerGroups()->toArray();
        $this->data['town_id']['options'] =statesByCountry(config('app.DEFAULT_COUNTRY_ID'))->toArray();
    }


    public static function validateRules() : array
    {
        return [
            'formData.user.firstname' => 'required',
            'formData.user.lastname'  => 'required',
            'formData.user.email'       => 'required|email',
            'formData.user.password'    => 'required|min:6|max:36',
            'formData.user.phone'       => 'required',
            //validating wholesales information

            'formData.wholesale.business_name'   => 'required',
            'formData.wholesale.cac_document' => 'required',
            'formData.wholesale.premises_licence' => 'required',
            'formData.wholesale.customer_type_id' =>'required',
            'formData.wholesale.customer_group_id' =>'required',
            'formData.wholesale.phone' =>'required',
            'formData.wholesale.business_email_address' =>'required',


            'formData.wholesale.address_1' =>'required',
            'formData.wholesale.state_id' =>'required',
            'formData.wholesale.town_id' =>'required',

        ];
    }

    public static function validateMessages() : array
    {
        return [
            'formData.user.firstname.required' => 'Please enter customers First Name',
            'formData.user.lastname.required'  => 'Please enter customers Last Name',
            'formData.user.email.required'       => 'Please enter customers Email',
            'formData.user.email.email'       => 'Please enter a valid Email Address',
            'formData.user.password.required'    => 'Please enter Password',
            'formData.user.phone.required'    => 'Please enter Phone Number',
            'formData.user.password.min'    => 'Password must be between 6 and 36 character',
            'formData.user.password.max'    => 'Password must be between 6 and 36 character',
            //validating wholesales information

            'formData.wholesale.business_name.required'   => 'Please enter Business Name',
            'formData.wholesale.business_cac_certificate.sometimes' => 'Please upload pdf or and image file supported extension (jpeg,jpg,bmp,png,gif,svg,pdf)',
            'formData.wholesale.business_premises_license.sometimes' => 'Please upload pdf or and image file supported extension (jpeg,jpg,bmp,png,gif,svg,pdf)',
            'formData.wholesale.customer_type_id.required' =>'Please select business type',
            'formData.wholesale.customer_group_id.required' =>'Please select business group',
            'formData.wholesale.phone.required' =>'Please enter business phone number',
            'formData.wholesale.business_email_address.required' =>'Please enter business email address',
            'formData.wholesale.business_email_address.email' =>'Please enter valid business email address',


            'formData.wholesale.address_1.required' => 'Please enter business Address',
            'formData.wholesale.state_id.required' => 'Please select business state',
            'formData.wholesale.town_id.required' => 'Please select business Town',

        ];
    }

    public function render()
    {
        if($this->formData['wholesale']['state_id'] != "0") {
            $this->data['town_id']['options'] = towns()->filter(function ($item) {
                return $item->state_id == $this->formData['wholesale']['state_id'];
            })->values()->toArray();
        }

        return view('livewire.backend.component.whole-sales.whole-sales-customer-form-component');
    }

    public function openModal()
    {
        $this->dispatch('openWholesalesCustomerModal');
    }


    public final function save()
    {

        $this->validate(self::validateRules(), self::validateMessages());

        return DB::transaction(function(){

            if(isset($this->formData['wholesale']['premises_licence'])){
                $premises_licence = md5($this->formData['wholesale']['premises_licence'] . microtime()).'.'.$this->formData['wholesale']['premises_licence']->extension();
                $this->formData['wholesale']['premises_licence']->storeAs('documents', $premises_licence);
            }

            if(isset($this->formData['wholesale']['cac_document'])){
                $cac_document = md5($this->formData['wholesale']['cac_document'] . microtime()).'.'.$this->formData['wholesale']['cac_document']->extension();
                $this->formData['wholesale']['cac_document']->storeAs('documents', $cac_document);
            }

            $user = $this->userAccountService->createUserAccount($this->formData['user']);

            $wholesale = $this->wholeSalesCustomerService->createCustomer($this->formData['wholesale'] ,$user);

            $app = $this->appUserService->createAppUser($user, $wholesale);

            $address = $this->addressService->createAddress($user, $this->formData['wholesale']);

            $this->wholeSalesCustomerService->attachDefaultAddressToCustomer($address, $wholesale);

            $this->alert(
                "success",
                "Customer has been created successfully",
            );

            $this->dispatch('creatingCustomerOnSuccess');

            return $user;

        });

    }


    public function update()
    {

    }
}
