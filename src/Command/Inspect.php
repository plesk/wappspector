<?php

namespace Plesk\Wappspector\Command;

use JsonException;
use Plesk\Wappspector\Helper\ScanDirectoryIterator;
use Plesk\Wappspector\MatchResult\CpanelWebApp;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\Wappspector;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
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
        $this->addOption(
            'max',
            '',
            InputOption::VALUE_REQUIRED,
            'Maximum number of technologies that can be found for directory. Default = 0 (no limit)',
            0
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $isJson = (bool)$input->getOption('json');
        $logger = new ConsoleLogger($output);
        $result = [];
        $matchersLimit = (int)$input->getOption('max');

        try {
            foreach ($this->getPath($input) as $path) {
                $result = [...$result, ...$this->wappspector->run($path, '/', $matchersLimit)];
            }
            $result = $this->filterResults($result);

            if ($isJson) {
                $this->jsonOutput($output, $result);
                return Command::SUCCESS;
            }

            $this->tableOutput($output, $result);

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            // The class matters as much as the message here: some of these carry no
            // message at all, which would otherwise report a bare empty line.
            $logger->error(sprintf('%s: %s', $exception::class, $exception->getMessage()));
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

    /**
     * @param MatchResultInterface[] $matchers
     * @return void
     */
    private function tableOutput(OutputInterface $output, array $matchers): void
    {
        $rows = [];

        foreach ($matchers as $matchResult) {
            $rows[] = [
                $matchResult->getId(),
                $matchResult->getName(),
                $matchResult->getPath(),
                $matchResult->getVersion() ?? '-',
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Technology', 'Path', 'Version'])
            ->setRows($rows);
        $table->render();
    }

    private function getPath(InputInterface $input): iterable
    {
        $path = $input->getArgument('path');

        // Say which path is wrong. Left to itself, a non-existent path reaches the
        // iterator below as an empty string and reports that an argument "must not be
        // empty", which names neither the path nor the problem.
        if (($realPath = realpath($path)) === false) {
            throw new InvalidArgumentException(sprintf('The path "%s" does not exist or is not readable.', $path));
        }

        $path = $realPath;
        if (!$input->getOption('recursive')) {
            yield $path;
            return;
        }

        $it = new ScanDirectoryIterator($path, (int)$input->getOption('depth'));

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

    /**
     * @param MatchResultInterface[] $result
     * @return MatchResultInterface[]
     */
    private function filterResults(array $result): array
    {
        $result = $this->dropWhatWebApplicationsAreBuiltWith($result);

        return array_values(
            array_filter($result, static function (MatchResultInterface $matcher) {
                static $uniq = [];
                $key = $matcher->getId() . ':' . $matcher->getPath();
                if (array_key_exists($key, $uniq)) {
                    return false;
                }
                $uniq[$key] = true;
                return true;
            })
        );
    }

    /**
     * A directory registered as a web application is reported as that application and
     * nothing else. The registry is the authority on what such a directory is, so what
     * it happens to be built with adds nothing, and reporting both lists one
     * application twice.
     *
     * The highest-priority result wins for these paths only. Every other path still
     * reports every technology matched there, the way a Laravel site is also reported
     * as Composer, PHP and JS.
     *
     * @param MatchResultInterface[] $results
     * @return MatchResultInterface[]
     */
    private function dropWhatWebApplicationsAreBuiltWith(array $results): array
    {
        $webAppPaths = [];

        foreach ($results as $result) {
            if (!$result instanceof CpanelWebApp) {
                continue;
            }

            $webAppPaths[$result->getPath()] = true;
        }

        return array_filter(
            $results,
            static fn(MatchResultInterface $result): bool => $result instanceof CpanelWebApp
                || !isset($webAppPaths[$result->getPath()])
        );
    }
}
