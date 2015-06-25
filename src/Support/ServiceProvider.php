<?php

namespace Sharks\Support;

use Sharks\Console\Token;
use Sharks\Console\Up;
use Sharks\Console\Down;
use Sharks\Providers\DigitalOcean;
use League\Container\ServiceProvider as LeagueServiceProvider;

class ServiceProvider extends LeagueServiceProvider
{
    /**
     * This array allows the container to be aware of
     * what your service provider actually provides,
     * this should contain all alias names that
     * you plan to register with the container
     *
     * @var array
     */
    protected $provides = ['up', 'down', 'token'];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()['up'] = new Up(new DigitalOcean);
        $this->getContainer()['down'] = new Down(new DigitalOcean);
        $this->getContainer()['token'] = new Token();
    }
}