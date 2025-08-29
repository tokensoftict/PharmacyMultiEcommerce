<?php

namespace App\Services\User;

use App\Classes\NotificationManager\NewAccountNotificationManager;
use App\Mail\Administrator\AdministratorInvitationMail;
use App\Mail\SalesRep\SalesRepInvitationMail;
use App\Models\AppUser;
use App\Models\SalesRepresentative;
use App\Models\SupermarketAdmin;
use App\Models\User;
use App\Models\WholesalesAdmin;
use App\Notifications\NewAccountRegistration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Laravolt\Avatar\Facade as Avatar;

class UserAccountService
{

    /**
     * @param array $data
     * @return User
     */
    public final function createUserAccount(array $data) : User
    {

        $data['firstname'] = ucwords(strtolower($data['firstname']));
        $data['lastname'] = ucwords(strtolower($data['lastname']));
        $data['email'] = ucwords(strtolower($data['email']));

        $user = User::where("email", $data['email'])->first();

        if(!$user) $user = User::where("phone", $data['phone'])->first();

        if(!app()->runningInConsole()) {
            $data['password'] = bcrypt($data['password']);
        }

        $data['phone'] = normalizePhoneNumber($data['phone']);
        $data['phone'] = str_replace("-", "", $data['phone']);
        $data['phone'] = str_replace(" ", "", $data['phone']);

        if(!$user){

            $user = User::create($data);

            if (!file_exists(public_path('storage/users'))) {
                mkdir(public_path('storage/users'), 0777, true);
            }

            Avatar::create($user->name)->save(public_path("storage/users/$user->id.png"));
            $user->image = "storage/users/$user->id.png";
            $user->update();

            $user->updateLastSeen();
            $verifyFields = [];

            if(!is_null($user->phone)){
                $verifyFields[] = false; //"phone";
            }

            if(!is_null($user->email)){
                $verifyFields[] = "email";
            }

            if(!app()->runningInConsole()) {
                NewAccountNotificationManager::notifyAll($user, $verifyFields);
            }

        }else{

            $user = $this->updateUserAccount($user->id , $data);

        }

        return $user;
    }


    /**
     * @param int $id
     * @param array $data
     * @return User|bool
     */
    public final function updateUserAccount(int $id, array $data) : User
    {
        $user = User::findorfail($id);

        if($user){

            $user->update($data);
        }

        return $user->fresh();
    }


    /**
     * @param User $user
     * @return User
     */
    public final function activateUserAccount(User $user) : User
    {
        $user->email_verified_at = now();
        $user->phone_verified_at = now();

        $user->update();

        return $user->fresh();
    }

    /**
     * @param User $user
     * @param array $data
     * @param bool $sendMail
     * @return SalesRepresentative
     */
    public final function createSalesRepAccount(User $user, array $data, bool $sendMail=true) : SalesRepresentative
    {
        $data = Arr::only($data, [
            'status',
            'user_id',
            'invitation_status',
            "invitation_sent_date",
            'added_by',
            'token',
            'code',
            'invitation_approval_date',
            'old_server_id'
        ]);

        $data['code'] = generateUniqueReferralCode();

        if($sendMail && !isset($data['token'])){
            $data['token'] = sha1(md5(generateRandomString(50)));
        }

        $rep = SalesRepresentative::updateOrCreate(['user_id' => $user->id], $data);

        app(AppUserService::class)->createAppUser($user, $rep);

        if($sendMail){
            //trigger invent to send invite email to the user
            $link = route('sales-representative.sales_rep.accept-invitation', $rep->token);
            Mail::to($rep->user->email)->send(new SalesRepInvitationMail($rep, $link));
        }

        return  $rep;
    }


    /**
     * @param User $user
     * @param array $data
     * @param bool $sendMail
     * @return WholesalesAdmin
     */
    public final function createWholesalesAdministrator(User $user, array $data, bool $sendMail=true) : WholesalesAdmin
    {
        $data = Arr::only($data, [
            'status',
            'user_id',
            'invitation_status',
            "invitation_sent_date",
            "invitation_approval_date",
            'added_by',
            'token',
        ]);

        if($sendMail && !isset($data['token'])){
            $data['token'] = sha1(md5(generateRandomString(50)));
        }

        $admin = WholesalesAdmin::updateOrCreate(['user_id' => $user->id], $data);
        app(AppUserService::class)->createAppUser($user, $admin);

        if($sendMail){
            $link = route('administrator.admin.accept-invitation', $admin->token);
            Mail::to($admin->user->email)->send(new AdministratorInvitationMail($admin, $link));
        }

        return  $admin;
    }


    /**
     * @param User $user
     * @param array $data
     * @param bool $sendMail
     * @return SupermarketAdmin
     */
    public final function createSuperMarketAdministrator(User $user, array $data, bool $sendMail=true) : SupermarketAdmin
    {
        $data = Arr::only($data, [
            'status',
            'user_id',
            'invitation_status',
            "invitation_sent_date",
            'added_by',
            'token',
        ]);

        if($sendMail && !isset($data['token'])){
            $data['token'] = sha1(md5(generateRandomString(50)));
        }

        $admin = SupermarketAdmin::updateOrCreate(['user_id' => $user->id], $data);
        app(AppUserService::class)->createAppUser($user, $admin);

        if($sendMail){
            $link = route('administrator.admin.accept-invitation', $admin->token);
            Mail::to($admin->user->email)->send(new AdministratorInvitationMail($admin, $link));
        }

        return  $admin;
    }
}
