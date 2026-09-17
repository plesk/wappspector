<?php

declare(strict_types=1);

namespace Test\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\Command\Inspect;
use Plesk\Wappspector\DIContainer;
use Plesk\Wappspector\Helper\ScanDirectoryIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(Inspect::class)]
#[CoversClass(ScanDirectoryIterator::class)]
class InspectTest extends TestCase
{
    /**
     * A registered application is reported exactly once as a web application, so
     * scanning an account yields one such row per application and the rows can be
     * counted. What it is built with is reported after it, the way a Laravel site is
     * also reported as Composer and PHP.
     */
    public function testReportsAWebApplicationOnceAheadOfItsContents(): void
    {
        $results = $this->inspect('cpanelwebapp');
        $app = 'ea-podman.d/blog.user.01/webapp';

        $this->assertSame(
            [['id' => 'cpanelwebapp', 'application' => 'blog'], ['id' => 'php', 'application' => null]],
            $this->resultsFor($results, $app)
        );
    }

    /**
     * `--max 1` keeps the first match only, and the web application matcher runs first,
     * so a caller that wants applications alone gets one row per application.
     */
    public function testAMaximumOfOneLeavesTheWebApplication(): void
    {
        $results = $this->inspect('cpanelwebapp', ['--max' => 1]);

        $this->assertSame(
            [['id' => 'cpanelwebapp', 'application' => 'blog']],
            $this->resultsFor($results, 'ea-podman.d/blog.user.01/webapp')
        );
    }

    /**
     * A redeploy leaves the previous container behind as `<container>.bak`. It is not
     * in the registry, so it is never reported as an application — otherwise every
     * superseded deploy would be counted as a live site.
     */
    public function testReportsNoApplicationForALeftOverContainer(): void
    {
        $results = $this->inspect('cpanelwebapp');

        $this->assertSame(
            [['id' => 'php', 'application' => null]],
            $this->resultsFor($results, 'ea-podman.d/oldapp.user.09.bak/webapp')
        );
    }

    public function testStillReportsTechnologiesOutsideContainers(): void
    {
        $results = $this->inspect('php');

        $this->assertNotSame([], $results, 'a plain directory is unaffected by web application detection');
    }

    public function testNamesAPathItCannotRead(): void
    {
        $tester = new CommandTester(DIContainer::build()->get(Inspect::class));
        $tester->execute(['path' => '/no/such/path']);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('/no/such/path', $tester->getDisplay());
    }

    /**
     * @return array<int, array{id: string, application: ?string}>
     */
    private function resultsFor(array $results, string $relativePath): array
    {
        $found = [];

        foreach ($results as $result) {
            if (str_ends_with($result['path'], '/' . $relativePath)) {
                $found[] = ['id' => $result['id'], 'application' => $result['application'] ?? null];
            }
        }

        return $found;
    }

    /**
     * @return array<int, array<string, mixed>> The command's JSON output, decoded
     */
    private function inspect(string $fixture, array $options = []): array
    {
        $tester = new CommandTester(DIContainer::build()->get(Inspect::class));
        $tester->execute(['path' => TESTS_DIR . '/../test-data/' . $fixture, '--json' => true, ...$options]);
        $tester->assertCommandIsSuccessful();

        return json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR);
    }
}
