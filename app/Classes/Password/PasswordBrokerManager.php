<?php

namespace App\Classes\Password;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\PasswordBrokerFactory as FactoryContract;
use InvalidArgumentException;

class PasswordBrokerManager implements FactoryContract
{

    protected $app;

    protected $brokers = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function broker($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->brokers[$name] ?? ($this->brokers[$name] = $this->resolve($name));
    }


    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password re-setter [{$name}] is not defined.");
        }

        return new PasswordBroker(
            $this->createTokenRepository($config),
            $this->createMobilePinRepository($config),
            $this->app['auth']->createUserProvider($config['provider'] ?? null),
            $this->app['events'] ?? null,
        );
    }



    protected function createMobilePinRepository(array $config)
    {
        $connection = $config['connection'] ?? null;

        return new DatabaseMobilePinRepository(
            $this->app['db']->connection($connection),
            $config['table'],
        );
    }

    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire'],
            $config['throttle'] ?? 0
        );
    }

    protected function getConfig($name)
    {
        return $this->app['config']["auth.passwords.{$name}"];
    }

    public function getDefaultDriver()
    {
        return $this->app['config']['auth.defaults.passwords'];
    }

    public function setDefaultDriver($name)
    {
        $this->app['config']['auth.defaults.passwords'] = $name;
    }


    public function __call($method, $parameters)
    {
        return $this->broker()->{$method}(...$parameters);
    }
}
