<?php

namespace App\Classes\Password;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Carbon;

class DatabaseMobilePinRepository implements MobilePinRepositoryInterface
{
    protected $connection;

    protected $table;
    protected $expires;

    protected $throttle;

    public function __construct(ConnectionInterface $connection,
                                                    $table, $expires = 60,
                                                    $throttle = 60)
    {
        $this->table = $table;
        $this->expires = $expires * 60;
        $this->connection = $connection;
        $this->throttle = $throttle;
    }

    public function create(CanResetPasswordByTokenInterface $user)
    {
        $phone = $user->getPhoneForPasswordReset();

        $this->deleteExisting($user);

        $pin = $this->createNewPin();

        $this->getTable()->insert($this->getPayload($phone, $pin, $user->email));

        return $pin;
    }

    public function exists(CanResetPasswordByTokenInterface $user, $token)
    {
        $record = (array) $this->getTable()->where(
            'phone', $user->getPhoneForPasswordReset()
        )->where("verification_reset_code", $token)->first();

        return $record &&
            ! $this->pinExpired($record['created_at']);
    }

    protected function pinExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    public function recentlyCreatedMobileResetPin(CanResetPasswordByTokenInterface $user)
    {
        $record = (array) $this->getTable()->where(
            'phone', $user->getPhoneForPasswordReset()
        )->first();

        return $record && $this->pinRecentlyCreated($record['created_at']);
    }

    public function delete(CanResetPasswordByTokenInterface $user)
    {
        $this->deleteExisting($user);
    }


    protected function deleteExisting(CanResetPasswordByTokenInterface $user)
    {
        $this->getTable()->where('email', $user->email)->delete();
        return $this->getTable()->where('phone', $user->getPhoneForPasswordReset())->delete();
    }

    protected function pinRecentlyCreated($createdAt)
    {
        if ($this->throttle <= 0) {
            return false;
        }

        return Carbon::parse($createdAt)->addSeconds(
            $this->throttle
        )->isFuture();
    }



    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    public function createNewPin()
    {
        return mt_rand(1000, 9999);
    }

    protected function getPayload($phone, $pin, $email)
    {
        return ['phone' => $phone, 'email' => $email ,'verification_reset_code' => $pin, 'created_at' => new Carbon];
    }

    protected function getTable()
    {
        return $this->connection->table($this->table);
    }

    public function recentlyCreatedToken(CanResetPasswordByTokenInterface $user)
    {
        return $this->recentlyCreatedMobileResetPin($user);
    }
}
