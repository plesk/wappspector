<?php

namespace Plesk\Wappspector\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'wappspector:analyze')]
class Analyze extends Command
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        return Command::SUCCESS;
    }
}
