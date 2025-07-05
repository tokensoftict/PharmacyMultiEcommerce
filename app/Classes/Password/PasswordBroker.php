<?php

namespace App\Classes\Password;

use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Closure;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use UnexpectedValueException;

class PasswordBroker implements PasswordBrokerContract
{
    protected $tokens;

    protected $pins;

    protected $users;

    protected $events;

    public function __construct(TokenRepositoryInterface $tokens, MobilePinRepositoryInterface $pins ,UserProvider $users, ?Dispatcher $dispatcher = null)
    {
        $this->users = $users;
        $this->tokens = $tokens;
        $this->events = $dispatcher;
        $this->pins = $pins;
    }

    public function sendResetLink(array $credentials, ?Closure $callback = null)
    {
        if (Arr::has($credentials,"phone")) {
            return $this->sendMobileResetPin($credentials, $callback);
        }

        return  $this->sendEmailResetLink($credentials, $callback);
    }


    protected function sendMobileResetPin(array $credentials, ?Closure $callback = null)
    {
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if($this->pins->recentlyCreatedMobileResetPin($user)){
            return static::RESET_THROTTLED;
        }


        $pin = $this->pins->create($user);
        $token = $this->tokens->create($user);
        DB::table('password_reset_tokens')->where(function($query) use ($user){
            $query
                ->orWhere('email', $user->email)
                ->orWhere('phone', $user->phone);
        })->update(['verification_reset_code' => $pin, 'phone' => $user->phone, 'email' => $user->email]);

        if ($callback) {
            return $callback($user, $pin) ?? static::RESET_LINK_SENT;
        }

        $user->sendEmailAndMobileNotificationForPasswordReset($pin, $token);

        if ($this->events) {
            $this->events->dispatch(new PasswordResetLinkSent($user));
        }

        return static::RESET_LINK_SENT;
    }


    protected function sendEmailResetLink(array $credentials, ?Closure $callback = null)
    {
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if ($this->tokens->recentlyCreatedToken($user)) {
            return static::RESET_THROTTLED;
        }

        $pin = $this->pins->create($user);
        $token = $this->tokens->create($user);
        DB::table('password_reset_tokens')->where(function($query) use ($user){
            $query
                ->orWhere('email', $user->email)
                ->orWhere('phone', $user->phone);
        })->update(['verification_reset_code' => $pin, 'phone' => $user->phone, 'email' => $user->email]);


        if ($callback) {
            return $callback($user, $token) ?? static::RESET_LINK_SENT;
        }

        $user->sendEmailAndMobileNotificationForPasswordReset($pin, $token);

        if ($this->events) {
            $this->events->dispatch(new PasswordResetLinkSent($user));
        }

        return static::RESET_LINK_SENT;
    }


    public function getUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token', "verification_reset_code", "pin"]);

        if(isset($credentials["phone"]) and !is_numeric($credentials['phone'])){
            $credentials['email'] = $credentials['phone'];
            unset($credentials['phone']);
        }

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof CanResetPasswordContract) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }



    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (isset($credentials['token']) && ! $this->tokens->exists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        if(isset($credentials['pin']) && ! $this->pins->exists($user, $credentials['pin'])){
            return static::INVALID_TOKEN;
        }
        return $user;
    }


    public function reset(array $credentials, Closure $callback)
    {
        $user = $this->validateReset($credentials);

        if (! $user instanceof CanResetPasswordContract && ! $user instanceof CanResetPasswordByTokenInterface) {
            return $user;
        }

        $password = $credentials['password'];

        $callback($user, $password);

        if($user instanceof CanResetPasswordContract) {
            $this->tokens->delete($user);
        }

        if($user instanceof CanResetPasswordByTokenInterface){
            $this->pins->delete($user);
        }

        return static::PASSWORD_RESET;
    }

}
