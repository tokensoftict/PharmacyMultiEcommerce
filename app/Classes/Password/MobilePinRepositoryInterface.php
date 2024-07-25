<?php

namespace App\Classes\Password;

use App\Classes\Password\CanResetPasswordByTokenInterface as CanResetPasswordByMobileContract;


interface MobilePinRepositoryInterface
{
    /**
     * @param CanResetPasswordByTokenInterface $user
     * @return mixed
     */
    public function create(CanResetPasswordByMobileContract $user);


    /**
     * @param CanResetPasswordByTokenInterface $user
     * @param $token
     * @return mixed
     */
    public function exists(CanResetPasswordByMobileContract $user, $token);


    /**
     * @param CanResetPasswordByTokenInterface $user
     * @return mixed
     */
    public function recentlyCreatedToken(CanResetPasswordByMobileContract $user);


    /**
     * @param CanResetPasswordByTokenInterface $user
     * @return mixed
     */
    public function delete(CanResetPasswordByMobileContract $user);

    /**
     * @return mixed
     */
    public function deleteExpired();
}
