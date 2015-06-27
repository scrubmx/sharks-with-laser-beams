<?php

namespace Sharks\Console;

use Sharks\Support\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to add the API token to the configuration file.
 *
 * @package Sharks\Console
 */
class Token extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    public function configure()
    {
        $this->setName('token')
             ->setDescription('Add the API token to the configuration file.')
             ->addArgument('token', InputArgument::REQUIRED, 'The DigitalOcean API token.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface  $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        Config::set('api_token', $input->getArgument('token'));

        $output->writeln("<info>The new token was set!</info>");
    }
}
