<?php

namespace App\Services\User\Wholesales;

use App\Mail\Customer\SendActivationEmail;
use App\Mail\Customer\WholesalesAccountRegistration;
use App\Mail\SalesRep\NewCustomer;
use App\Models\Address;
use App\Models\SalesRepresentative;
use App\Models\User;
use App\Models\WholesalesUser;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class WholeSalesCustomerService
{

    public String $userPassword = "";

    /**
     * @param array $data
     * @param User $user
     * @return WholesalesUser
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public final function createCustomer(array $data, User $user) : WholesalesUser
    {
        $customer = WholesalesUser::where("user_id", $user->id)->first();

        if(!$customer) {
            $data = Arr::only(
                $data,
                ['customer_group_id', 'customer_type_id','business_name', 'type', 'cac_document', 'premises_licence', 'phone', 'address_id']
            );

            $data['user_id'] = $user->id;
            $customer  = WholesalesUser::create($data);

            if(request()->has("cac_document")) {
                $cac = business_certificate()->addMediaFromRequest("cac_document")->toMediaCollection('medialibrary');
                $customer->cac_document = getMediaFullPath($cac);
            }
            if(request()->has("premises_licence")) {
                $premises = premises_licence()->addMediaFromRequest("premises_licence")->toMediaCollection('medialibrary');
                $customer->premises_licence = getMediaFullPath($premises);
            }

            if(Arr::has($data, "referral_code")) {
                $salesRep = SalesRepresentative::where("code", $data["referral_code"])->first();
                if($salesRep) {
                   $customer->sales_representative_id = $salesRep->id;
                }
            }

            if(request()->has("referral_code") and !Arr::has($data, "referral_code")) {
                $salesRep = SalesRepresentative::where("code", request()->get('referral_code'))->first();
                if($salesRep) {
                    $customer->sales_representative_id = $salesRep->id;
                }
            }

            $customer->save();
            $customer->fresh();

        }else{
            $customer = $this->updateCustomer($customer->id, $data);
        }

        if(!is_null($customer->sales_representative_id) and isset($salesRep)) {
            Mail::to($salesRep->user->email)->send(new NewCustomer($salesRep, $customer));
        }

        Mail::to($customer->user->email)->send(new WholesalesAccountRegistration($customer, $this->userPassword));

        return $customer;
    }


    /**
     * @param int $id
     * @param array $data
     * @return WholesalesUser
     */
    public final function updateCustomer(int $id, array $data) : WholesalesUser
    {
        $customer = WholesalesUser::findorfail($id);

        if($customer)
        {
            $customer->update($data);
        }

        return $customer->fresh();
    }


    /**
     * @param WholesalesUser $user
     * @return WholesalesUser
     */
    public final function activateBusiness(WholesalesUser $user) : WholesalesUser
    {
        $user->status = !$user->status;
        $user->save();
        $user->fresh();

        if($user->status) {
            Mail::to($user->user->email)->send(new SendActivationEmail($user));
        }

        return $user;
    }


    /**
     * @param Address $address
     * @param WholesalesUser $wholesalesUser
     * @return WholesalesUser
     */
    public final function attachDefaultAddressToCustomer(Address $address, WholesalesUser $wholesalesUser) : WholesalesUser
    {
        $wholesalesUser->address_id = $address->id;
        $wholesalesUser->update();
        return $wholesalesUser->fresh();
    }

}
