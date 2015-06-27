<?php

namespace Sharks\Console;

use Sharks\Providers\DigitalOcean;
use Sharks\Storage\Config;
use Sharks\Storage\Droplet;
use Sharks\Support\Exceptions\MissingApiTokenException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to start a batch of load testing servers.
 *
 * @package Sharks\Console
 */
class Up extends Command
{
    /**
     * The default number of instances to create.
     *
     * @var string
     */
    const NUMBER_OF_INSTANCES = 2;

    /**
     * @var DigitalOcean
     */
    private $provider;

    /**
     * @param \Sharks\Providers\DigitalOcean $provider
     */
    public function __construct(DigitalOcean $provider)
    {
        $this->provider = $provider;

        parent::__construct();
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function configure()
    {
        $this->setName('up')
             ->setDescription('Start a batch of load testing servers.')
             ->addArgument(
                 'amount',                          // Name of the argument
                 InputArgument::OPTIONAL,           // Argument mode
                 'The number of servers to start.', // Description
                 self::NUMBER_OF_INSTANCES          // Default value
             )
             ->addOption(
                 'key',                                                     // Name of the option
                 'k',                                                       // Short version
                 InputOption::VALUE_OPTIONAL,                               // Option mode
                 'The path to the public ssh key to connect to the sharks', // Description
                 '~/.ssh/id_rsa.pub'                                        // Default value
             )
             ->addOption(
                 'token',                     // Name of the option
                 't',                         // Short version
                 InputOption::VALUE_OPTIONAL, // Option mode
                 'DigitalOcean API Token.',   // Description
                 NULL                         // Default value
             );
    }

    /**
     * Executes the current command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return null|int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $amount = $input->getArgument('amount');

        $output->writeln("<info>Setting up {$amount} sharks to attack the victim...</info>");

        // Create returns an array with all the responses from digitalocean api.
        $responses = $this->provider->create(
            $amount,
            $this->getToken($input),
            $input->getOption('key')
        );

        $this->saveResponseData($responses);

        $this->waitUntilDropletsAreActive($output);

        $output->writeln("<info>The sharks are ready to attack the victim!</info>");
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \Sharks\Support\Exceptions\MissingApiTokenException
     *
     * @return mixed
     */
    private function getToken(InputInterface $input)
    {
        $token = $input->getOption('token');

         if ( ! is_null($token)) {
             Config::set('api_token', $token);
         } else {
             $token = Config::get('api_token');
         }

        if (is_null($token)) {
            throw new MissingApiTokenException("You need an API Token duh!");
        }

        return $token;
    }

    /**
     * @param $responses
     */
    private function saveResponseData(array $responses)
    {
        $ids = array_map(function($response){
            $response = json_decode($response->getBody()->getContents());
            return $response->droplet->id;
        }, $responses);

        $data = $this->provider->report($ids);

        Droplet::save($data);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function waitUntilDropletsAreActive(OutputInterface $output)
    {
        $ready = false;

        while( ! $ready) {
            $output->write('.');
            sleep(2);
            $droplets = $this->provider->report();

            foreach($droplets as $droplet) {
                if ( $droplet->status === 'active' ){
                    $ready = true;

                    Droplet::save($droplets);
                }
            }
        }

        $output->writeln('.');
    }
}
