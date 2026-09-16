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
     * A web application is reported once, as a web application. Its `index.php` stands
     * in for whatever an application happens to contain: a container is reported as the
     * application it is, never as the technologies inside it, so scanning an account
     * yields one row per application and the rows can be counted.
     */
    public function testReportsAWebApplicationOnceAndNotItsContents(): void
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
     * A redeploy leaves the previous container behind as `<container>.bak`. It is not
     * in the registry, so it is not an application, and nothing inside it may be
     * reported either — otherwise every superseded deploy shows up as a live site.
     */
    public function testReportsNothingForALeftOverContainer(): void
    {
        $results = $this->inspect('cpanelwebapp');

        $this->assertSame([], $this->resultsFor($results, 'ea-podman.d/oldapp.user.09.bak/webapp'));
    }

    public function testStillReportsTechnologiesOutsideContainers(): void
    {
        $results = $this->inspect('php');

        $this->assertNotSame([], $results, 'a plain directory is unaffected by container scoping');
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
                $found[] = ['id' => $result['id'], 'application' => $result['application']];
            }
        }

        return $found;
    }

    /**
     * @return array<int, array<string, mixed>> The command's JSON output, decoded
     */
    private function inspect(string $fixture): array
    {
        $tester = new CommandTester(DIContainer::build()->get(Inspect::class));
        $tester->execute(['path' => TESTS_DIR . '/../test-data/' . $fixture, '--json' => true]);
        $tester->assertCommandIsSuccessful();

        return json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR);
    }
}
