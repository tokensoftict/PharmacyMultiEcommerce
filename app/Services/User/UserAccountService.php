<?php

namespace App\Services\User;

use App\Classes\NotificationManager\NewAccountNotificationManager;
use App\Models\User;
use App\Notifications\NewAccountRegistration;
use Laravolt\Avatar\Facade as Avatar;

class UserAccountService
{

    /**
     * @param array $data
     * @return User
     */
    public final function createUserAccount(array $data) : User
    {
        $user = User::where("email", $data['email'])->first();

        if(!$user) $user = User::where("phone", $data['phone'])->first();

        $data['password'] = bcrypt($data['password']);

        $data['phone'] = str_replace("+234", "0", $data['phone']);

        $data['phone'] = str_replace("-", "", $data['phone']);

        if(!$user){

            $user = User::create($data);

            if (!file_exists(public_path('storage/users'))) {
                mkdir(public_path('storage/users'), 0777, true);
            }

            Avatar::create($user->name)->save(public_path("storage/users/$user->id.png"));
            $user->image = "storage/users/$user->id.png";
            $user->update();

            $user->updateLastSeen();

            if(!is_null($user->email)){
                $verifyFields[] = "email";
            }

            if(!is_null($user->phone)){
                $verifyFields[] = "phone";
            }

            NewAccountNotificationManager::notifyAll($user, $verifyFields);

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

        $user->update();

        return $user->fresh();
    }

}
