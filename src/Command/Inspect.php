<?php

namespace Plesk\Wappspector\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'wappspector:inspect')]
class Inspect extends Command
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = getcwd();
        $adapter = new \League\Flysystem\Local\LocalFilesystemAdapter($path);
        $fs = new \League\Flysystem\Filesystem($adapter);
        $matcher = new \Plesk\Wappspector\WappMatchers\PhpMatcher($fs);
        $results = $matcher->match('/');
        $results = [...$results, ...$matcher->match('/')];
        foreach ($results as $match) {
            $output->writeln(json_encode($match, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        }

        return Command::SUCCESS;
    }
}
