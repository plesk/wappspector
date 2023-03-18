<?php

namespace Plesk\Wappspector\Command;

use Plesk\Wappspector\Wappspector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(name: 'wappspector:inspect')]
class Inspect extends Command
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $wappSpectorResults = Wappspector::run(getcwd());

            $output->writeln(json_encode($wappSpectorResults, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            // TODO: log exception.
            return Command::FAILURE;
        }
    }
}
