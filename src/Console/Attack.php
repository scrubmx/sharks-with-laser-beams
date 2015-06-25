<?php

namespace Sharks\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Attack extends Command
{
    /**
     * The description of the command.
     *
     * @var string
     */
    private static $description = 'Begin the attack on a specific url.';

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function configure()
    {
        $this->setName('attack')
             ->setDescription(self::$description)
             ->addArgument('url', InputArgument::REQUIRED, 'URL of the target to attack.');
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
        $message = "<info>The sharks are firing their lasers — pew! pew!.</info>";

        return $output->writeln($message);
    }
}
