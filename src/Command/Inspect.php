<?php

namespace Plesk\Wappspector\Command;

use Plesk\Wappspector\Wappspector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(name: 'wappspector:inspect')]
class Inspect extends Command
{
    public function __construct(private Wappspector $wappspector)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);

        try {
            $result = $this->wappspector->run(getcwd() . '/test-data/joomla');

            $output->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $logger->error($exception->getMessage());
            return Command::FAILURE;
        }
    }
}
