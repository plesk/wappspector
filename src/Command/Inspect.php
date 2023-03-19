<?php

namespace Plesk\Wappspector\Command;

use Plesk\Wappspector\Wappspector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(name: 'wappspector:inspect')]
class Inspect extends Command
{
    public function __construct(private Wappspector $wappspector)
    {
        parent::__construct();
        $this->addOption('path', '', InputOption::VALUE_OPTIONAL, 'Root path', getcwd());
        $this->addOption('recursive', '', InputOption::VALUE_NEGATABLE, 'Traverse directories recursive', true);
        $this->addOption('depth', '', InputOption::VALUE_OPTIONAL, 'Depth of recurse', 1);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);
        $result = [];

        try {
            foreach ($this->getPath($input) as $path) {
                $result = [...$result, ...$this->wappspector->run($path)];
            }

            $output->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $logger->error($exception->getMessage());
            return Command::FAILURE;
        }
    }

    private function getPath(InputInterface $input): iterable
    {
        $path = $input->getOption('path');
        if (!$input->getOption('recursive')) {
            yield $path;
            return;
        }

        $flags = \FilesystemIterator::FOLLOW_SYMLINKS
            | \FilesystemIterator::KEY_AS_PATHNAME
            | \FilesystemIterator::CURRENT_AS_FILEINFO
            | \FilesystemIterator::SKIP_DOTS;
        $itFlags = \RecursiveIteratorIterator::SELF_FIRST;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, $flags), $itFlags);
        $it->setMaxDepth((int)$input->getOption('depth'));

        foreach ($it as $path => $item) {
            /** @var \SplFileInfo $item */
            if (str_contains($path, '/.')) {
                continue;
            }
            if (!$item->isDir()) {
                continue;
            }
            yield $path;
        }
    }
}
