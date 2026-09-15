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
     * A container is described by the web application matcher alone, whether the caller
     * came through the command or straight to the library. The command used to apply
     * this itself, which made the two disagree.
     */
    #[DataProvider('containerPathsProvider')]
    public function testAMatcherClaimingAPathIsTheOnlyOneAsked(string $path, array $expected): void
    {
        $this->assertSame($expected, $this->inspect($path));
    }

    public static function containerPathsProvider(): array
    {
        return [
            // Holds an index.php, which is not reported: it is the application's
            // business, not the account's.
            'a deployed application' => ['cpanelwebapp/ea-podman.d/blog.user.01/webapp', ['cpanelwebapp']],
            // A redeploy leaves this behind. It is in no registry, so it is nothing at
            // all -- not even the index.php it still holds.
            'a left-over container' => ['cpanelwebapp/ea-podman.d/oldapp.user.09.bak/webapp', []],
            'the container directory' => ['cpanelwebapp/ea-podman.d', []],
        ];
    }

    public function testAnUnclaimedPathIsOfferedToEveryMatcher(): void
    {
        $this->assertSame(['php'], $this->inspect('php/direct'));
    }

    /**
     * @return string[] The ids reported for the path, in matcher order
     */
    private function inspect(string $path): array
    {
        $results = DIContainer::build()
            ->get(Wappspector::class)
            ->run(realpath(TESTS_DIR . '/../test-data/' . $path));

        return array_map(
            static fn(MatchResultInterface $result): string => $result->getId(),
            [...$results]
        );
    }
}
