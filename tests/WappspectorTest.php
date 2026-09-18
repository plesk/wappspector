<?php

declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\DIContainer;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\Wappspector;

#[CoversClass(Wappspector::class)]
class WappspectorTest extends TestCase
{
    /**
     * Every matcher is asked about every path, so a web application is reported along
     * with what it is built with, the way a Laravel site is also reported as Composer
     * and PHP. The application comes first, so the caller can tell which one describes
     * the directory best.
     */
    #[DataProvider('containerPathsProvider')]
    public function testReportsTheApplicationAheadOfWhatItIsBuiltWith(string $path, array $expected): void
    {
        $this->assertSame($expected, $this->inspect($path));
    }

    public static function containerPathsProvider(): array
    {
        return [
            'a deployed application' => [
                'cpanelwebapp/ea-podman.d/blog.user.01/webapp',
                ['cpanelwebapp', 'php'],
            ],
            // A redeploy leaves this behind. It is in no registry, so it is not an
            // application -- only the PHP its left-over files are. A scan never reaches
            // it at all; see ScanDirectoryIterator.
            'a left-over container' => ['cpanelwebapp/ea-podman.d/oldapp.user.09.bak/webapp', ['php']],
            'the container directory' => ['cpanelwebapp/ea-podman.d', []],
        ];
    }

    /**
     * Matcher order is the priority order, so a limit of one leaves the best match.
     */
    public function testALimitKeepsTheBestMatch(): void
    {
        $this->assertSame(['cpanelwebapp'], $this->inspect('cpanelwebapp/ea-podman.d/blog.user.01/webapp', 1));
    }

    public function testEveryMatcherIsOfferedAPathOutsideAContainer(): void
    {
        $this->assertSame(['php'], $this->inspect('php/direct'));
    }

    /**
     * @return string[] The ids reported for the path, in matcher order
     */
    private function inspect(string $path, int $matchersLimit = 0): array
    {
        $results = DIContainer::build()
            ->get(Wappspector::class)
            ->run(realpath(TESTS_DIR . '/../test-data/' . $path), '/', $matchersLimit);

        return array_map(
            static fn(MatchResultInterface $result): string => $result->getId(),
            [...$results]
        );
    }
}
