<?php  namespace Sharks\Console;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to report the status of the load testing servers.
 *
 * @package Sharks\Console
 */
class Report extends Command
{

    /**
     * @var string
     */
    public $dropletUrl = 'https://cloud.digitalocean.com/droplets/{id}';

    /**
     * @{inheritDoc}
     */
    public function configure()
    {
        $this->setName('report')
             ->setDescription('Report the status of the load testing servers.');
    }
}
