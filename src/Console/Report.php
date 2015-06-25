<?php  namespace Sharks\Console;

use Sharks\Providers\DigitalOcean;
use Symfony\Component\Console\Helper\Table;
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
     * @var \Sharks\Providers\DigitalOcean
     */
    private $provider;

    /**
     * @var string
     */
    public $dropletUrl = 'https://cloud.digitalocean.com/droplets/{id}';

    /**
     * @param \Sharks\Providers\DigitalOcean $provider
     */
    function __construct(DigitalOcean $provider)
    {
        $this->provider = $provider;

        parent::__construct();
    }

    /**
     * @{inheritDoc}
     */
    public function configure()
    {
        $this->setName('report')
             ->setDescription('Report the status of the load testing servers.');
    }

    /**
     * Executes the current command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $headers = ['ID', 'Name', 'Status', 'IP', 'Region', 'Price Hourly'];

        $data = $this->provider->report();

        // Table expects an array of arrays, so we convert objects to arrays
        foreach($data as &$object) {
            $object = (array)$object;
        }

        (new Table($output))->setHeaders($headers)->setRows($data)->render();
    }
}