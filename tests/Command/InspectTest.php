<?php

declare(strict_types=1);

namespace Test\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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
     * A registered application is reported exactly once, as the application it is. Its
     * `index.php` stands in for whatever an application happens to be built with: the
     * registry already says what the directory is, so the lower-priority match is
     * dropped rather than listing the same application twice.
     */
    public function testReportsAWebApplicationAndNotWhatItIsBuiltWith(): void
    {
        $results = $this->inspect('cpanelwebapp');
        $app = 'ea-podman.d/blog.user.01/webapp';

        $this->assertSame(
            [['id' => 'cpanelwebapp', 'application' => 'blog']],
            $this->resultsFor($results, $app),
            'a web application holding an index.php must not also be reported as PHP'
        );
    }

    /**
     * A container directory holds the container's own plumbing -- the fixture keeps an
     * `index.php` there, as a real container keeps its configuration. It is walked
     * through to reach the application inside it, never inspected itself, so neither it
     * nor `ea-podman.d` is reported as a site of its own.
     */
    #[DataProvider('containerInternalsProvider')]
    public function testReportsNothingForAContainersOwnDirectories(string $relativePath): void
    {
        $this->assertSame([], $this->resultsFor($this->inspect('cpanelwebapp'), $relativePath));
    }

    public static function containerInternalsProvider(): array
    {
        return [
            'the container directory' => ['ea-podman.d'],
            'a container' => ['ea-podman.d/blog.user.01'],
            'a container holding no application' => ['ea-podman.d/notawebapp.user.03'],
        ];
    }

    /**
     * Only a web application's own path is narrowed to one row. Everywhere else every
     * match is still reported, so a Laravel site is reported as Composer too.
     */
    public function testStillReportsEveryTechnologyElsewhere(): void
    {
        $results = $this->inspect('laravel');

        $this->assertSame(
            [['id' => 'laravel', 'application' => null], ['id' => 'composer', 'application' => 'laravel/laravel']],
            $this->resultsFor($results, 'laravel10')
        );
    }

    /**
     * A redeploy leaves the previous container behind as `<container>.bak`. It is in no
     * registry, so it is not an application, and the scan leaves it out altogether:
     * what it still holds is a copy of the superseded deploy, not a live site.
     */
    public function testReportsNothingForALeftOverContainer(): void
    {
        $results = $this->inspect('cpanelwebapp');

        $this->assertSame([], $this->resultsFor($results, 'ea-podman.d/oldapp.user.09.bak/webapp'));
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
