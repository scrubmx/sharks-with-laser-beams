<?php  namespace Sharks\Console;

use GuzzleHttp\Client;
use Sharks\Providers\DigitalOcean;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to shutdown and deactivate the load testing servers.
 *
 * @package Sharks\Console
 */
class Down extends Command
{
    /**
     * @var DigitalOcean
     */
    private $provider;

    function __construct(DigitalOcean $provider)
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
        $this->setName('down')
             ->setDescription('Shutdown and deactivate the load testing servers.');
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
        $this->provider->down();
    }
}
