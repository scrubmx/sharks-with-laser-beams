<?php

namespace Sharks\Console;

use Sharks\Storage\Droplet;
use Sharks\Support\Exceptions\InvalidUrlException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Attack
 *
 * sharks attack -n 10000 -c 100
 *
 * @package Sharks\Console
 */
class Attack extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    public function configure()
    {
        $this->setName('attack')
             ->setDescription('Begin the attack on a specific url.')
             ->addArgument('url', InputArgument::REQUIRED, 'URL of the target to attack.')
             ->addOption(
                 'requests',                                              // Name of the option
                 'r',                                                     // Short version
                 InputOption::VALUE_OPTIONAL,                             // Option mode
                 'The number of total connections to make to the target', // Description
                 '1000'                                                   // Default value
             )
             ->addOption(
                 'concurrent',                                                 // Name of the option
                 'c',                                                          // Short version
                 InputOption::VALUE_OPTIONAL,                                  // Option mode
                 'The number of concurrent connections to make to the target', // Description
                 '100'                                                         // Default value
             );
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
        $url = $this->sanitizeUrl($input);
        $requests = $this->getNumberOfRequests($input);
        $concurrent = $input->getOption('concurrent');

        $results = [];

        foreach(Droplet::ips() as $index => $host) {
            exec("ssh root@$host 'sudo apt-get install apache2-utils -y > /dev/null'");
            $output->writeln("<info>The shark {$host} is firing the laser... pew! pew! pew!.</info>");
            $ab = exec("ssh root@$host 'ab -n {$requests} -c {$concurrent} {$url}'");

            $results[$index]['ip'] = $host;
            $results[$index]['requests'] = $requests;
            $results[$index]['longest'] = $ab;
        }

        (new Table($output))->setHeaders(['IP', 'Requests', 'Longest (ms)'])->setRows($results)->render();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \Sharks\Support\Exceptions\InvalidUrlException
     *
     * @return string
     */
    private function sanitizeUrl(InputInterface $input)
    {
        $url = $input->getArgument('url');

        if ( ! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidUrlException('You need to provide a valid url.');
        }

        return rtrim($url, '/') . '/';
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return float
     */
    private function getNumberOfRequests(InputInterface $input)
    {
        return ceil($input->getOption('requests') / Droplet::count());
    }
}
