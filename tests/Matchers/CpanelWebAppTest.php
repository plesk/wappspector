<?php

declare(strict_types=1);

namespace Test\Matchers;

use FilesystemIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Plesk\Wappspector\Matchers\CpanelWebApp;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\MatchResult\CpanelWebApp as MatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

#[CoversClass(CpanelWebApp::class)]
class CpanelWebAppTest extends AbstractMatcherTestCase
{
    private const HOME = 'cpanelwebapp';

    protected function getMatcherObj(): MatcherInterface
    {
        return new CpanelWebApp();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            'deployed' => [self::HOME . '/ea-podman.d/blog.user.01/webapp', null],
            'deployed, other app' => [self::HOME . '/ea-podman.d/shop.user.02/webapp', null],
            'staged' => [self::HOME . '/.cpanel/webapp-staging/draft/source', null],
            'trailing slash' => [self::HOME . '/ea-podman.d/blog.user.01/webapp/', null],
            'leading slash' => ['/' . self::HOME . '/ea-podman.d/blog.user.01/webapp', null],
        ];
    }

    #[DataProvider('applicationNamesProvider')]
    public function testReportsRegisteredApplicationName(string $path, string $name): void
    {
        $this->assertSame($name, $this->getMatch($path)->getApplication());
    }

    public static function applicationNamesProvider(): array
    {
        return [
            'deployed' => [self::HOME . '/ea-podman.d/blog.user.01/webapp', 'blog'],
            'deployed, other app' => [self::HOME . '/ea-podman.d/shop.user.02/webapp', 'shop'],
            'staged' => [self::HOME . '/.cpanel/webapp-staging/draft/source', 'draft'],
        ];
    }

    #[DataProvider('undetectablePathsProvider')]
    public function testNoMatch(string $path): void
    {
        $this->assertInstanceOf(EmptyMatchResult::class, $this->getMatch($path));
    }

    public static function undetectablePathsProvider(): array
    {
        return [
            'home directory itself' => [self::HOME],
            'unregistered directory under the home directory' => [self::HOME . '/public_html'],
            'container directory, above the application' => [self::HOME . '/ea-podman.d/blog.user.01'],
            'container that holds no application' => [self::HOME . '/ea-podman.d/notawebapp.user.03'],
            'unregistered container name' => [self::HOME . '/ea-podman.d/other.user.09/webapp'],
            'directory inside an application' => [self::HOME . '/ea-podman.d/shop.user.02/webapp/nested'],
            'staging directory of an unregistered application' => [
                self::HOME . '/.cpanel/webapp-staging/other/source',
            ],
            'no registry in any parent' => ['cpanelwebapp-no-registry/ea-podman.d/blog.nobody.01/webapp'],
            'malformed registry' => ['cpanelwebapp-broken/ea-podman.d/blog.broken.01/webapp'],
            'nonexistent path' => ['cpanelwebapp/nope'],
            'filesystem root' => [''],
        ];
    }

    /**
     * Counting deployed applications is the point of this matcher, so a traversal of a
     * whole account must report each one exactly once — never twice for nested
     * directories of the same application, and never for a directory that merely sits
     * near one.
     */
    public function testCountsEveryDeployedApplicationExactlyOnce(): void
    {
        $matcher = $this->getMatcherObj();
        $fs = $this->getFsObject();
        $found = [];

        foreach ($this->traverse(self::HOME) as $path) {
            $result = $matcher->match($fs, $path);

            if (!$result instanceof EmptyMatchResult) {
                $found[] = $result->getApplication();
            }
        }

        sort($found);

        // "draft" is staged rather than deployed, and its directory is under a
        // dot-directory that this traversal skips, exactly as the CLI does.
        $this->assertSame(['blog', 'shop'], $found);
    }

    /**
     * Every directory under `$root`, mirroring how the inspect command walks an
     * account: depth-first, dot-directories skipped.
     *
     * @return iterable<string> Paths relative to `test-data`
     */
    private function traverse(string $root): iterable
    {
        $base = TESTS_DIR . '/../test-data/';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base . $root, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        yield $root;

        /** @var SplFileInfo $item */
        foreach ($iterator as $path => $item) {
            $relative = substr($path, strlen($base));

            if ($item->isDir() && !str_contains($relative, '/.')) {
                yield $relative;
            }
        }
    }
}
