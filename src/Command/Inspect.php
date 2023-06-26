<?php

namespace Plesk\Wappspector\Command;

use FilesystemIterator;
use JsonException;
use Plesk\Wappspector\Wappspector;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
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
        $this->addArgument('path', InputArgument::OPTIONAL, 'Root path', getcwd());
        $this->addOption('json', '', InputOption::VALUE_NONE, 'JSON output');
        $this->addOption('recursive', '', InputOption::VALUE_NEGATABLE, 'Traverse directories recursive', true);
        $this->addOption('depth', '', InputOption::VALUE_OPTIONAL, 'Depth of recurse', 1);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $isJson = (bool)$input->getOption('json');
        $logger = new ConsoleLogger($output);
        $result = [];

        try {
            foreach ($this->getPath($input) as $path) {
                $result = [...$result, ...$this->wappspector->run($path)];
            }
            $result = $this->filterResults($result);

            if ($isJson) {
                $this->jsonOutput($output, $result);
                return Command::SUCCESS;
            }

            $this->tableOutput($output, $result);

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $logger->error($exception->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * @throws JsonException
     */
    private function jsonOutput(OutputInterface $output, array $result): void
    {
        $output->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }

    private function tableOutput(OutputInterface $output, array $result): void
    {
        $rows = [];

        foreach ($result as $item) {
            $rows[] = [$item['matcher'], $item['path'], $item['version'] ?? '-'];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Technology', 'Path', 'Version'])
            ->setRows($rows);
        $table->render();
    }

    private function getPath(InputInterface $input): iterable
    {
        $path = $input->getArgument('path');
        $path = realpath($path);
        if (!$input->getOption('recursive')) {
            yield $path;
            return;
        }

        $flags = FilesystemIterator::KEY_AS_PATHNAME
            | FilesystemIterator::CURRENT_AS_FILEINFO
            | FilesystemIterator::SKIP_DOTS;
        $itFlags = RecursiveIteratorIterator::SELF_FIRST;
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, $flags), $itFlags);
        $it->setMaxDepth((int)$input->getOption('depth'));

        foreach ($it as $path => $item) {
            /** @var SplFileInfo $item */
            if (str_contains($path, '/.')) {
                continue;
            }
            if (!$item->isDir()) {
                continue;
            }
            yield $path;
        }
    }

    private function filterResults(array $result): array
    {
        foreach ($result as &$match) {
            $match['path'] = $this->normalizePath($match['path']);
        }
        $result = array_values(array_filter($result, static function ($match) {
            static $uniq = [];
            if (isset($uniq[$match['matcher'] . $match['path']])) {
                return false;
            }
            $uniq[$match['matcher'] . $match['path']] = true;
            return true;
        }));
        return $result;
    }

    private function normalizePath(string $path): string
    {
        $parts = [];

        foreach (explode('/', $path) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;

                case '..':
                    if (empty($parts)) {
                        // invalid case
                        $parts[] = $part;
                        break;
                    }
                    array_pop($parts);
                    break;

                default:
                    $parts[] = $part;
                    break;
            }
        }

        return implode('/', $parts);
    }
}
