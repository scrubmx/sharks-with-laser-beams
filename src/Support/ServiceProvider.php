<?php

namespace Sharks\Support;

use Sharks\Console\Up;
use Sharks\Console\Down;
use Sharks\Console\Token;
use Sharks\Console\Report;
use Sharks\Console\Attack;
use Sharks\Providers\DigitalOcean;
use League\Container\ServiceProvider as LeagueServiceProvider;

class ServiceProvider extends LeagueServiceProvider
{
    /**
     * @var boolean
     */
    const SINGLETON = true;

    /**
     * This array allows the container to be aware of
     * what your service provider actually provides,
     * this should contain all alias names that
     * you plan to register with the container
     *
     * @var array
     */
    protected $provides = ['up', 'down', 'token', 'attack', 'report'];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->add('digitalocean', new DigitalOcean, self::SINGLETON);

        $digitalocean = $this->container->get('digitalocean');

        $this->container->add('up', new Up($digitalocean));
        $this->container->add('down', new Down($digitalocean));
        $this->container->add('report', new Report($digitalocean));
        $this->container->add('token', new Token);
        $this->container->add('attack', new Attack);
    }
}