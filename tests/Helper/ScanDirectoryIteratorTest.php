<?php

declare(strict_types=1);

namespace Test\Helper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\Helper\ScanDirectoryIterator;
use SplFileInfo;

#[CoversClass(ScanDirectoryIterator::class)]
class ScanDirectoryIteratorTest extends TestCase
{
    #[DataProvider('depthLimitedPathsProvider')]
    public function testHonoursTheDepthLimitOutsideContainerDirectories(string $path, int $depth, bool $found): void
    {
        $this->assertSame($found, in_array($path, $this->scan($depth), true));
    }

    public static function depthLimitedPathsProvider(): array
    {
        return [
            'at the limit' => ['cakephp/3', 1, true],
            'below the limit' => ['cakephp/3/bin', 1, false],
            'below the limit, raised' => ['cakephp/3/bin', 2, true],
        ];
    }

    /**
     * An application deployed by the cPanel Web App feature sits deeper than any useful
     * default depth, so the two levels under `ea-podman.d` are walked whatever the
     * limit is. Without this the feature would be invisible to a default scan.
     */
    #[DataProvider('containerPathsProvider')]
    public function testAlwaysDescendsIntoContainerDirectories(string $path, bool $found): void
    {
        $this->assertSame($found, in_array($path, $this->scan(1), true));
    }

    public static function containerPathsProvider(): array
    {
        return [
            'the container directory' => ['cpanelwebapp/ea-podman.d', true],
            'a container' => ['cpanelwebapp/ea-podman.d/blog.user.01', true],
            'an application, four levels down' => ['cpanelwebapp/ea-podman.d/blog.user.01/webapp', true],
            // Descent stops there: nothing below an application is an application, and
            // a container's contents can be arbitrarily large.
            'inside an application' => ['cpanelwebapp/ea-podman.d/shop.user.02/webapp/nested', false],
        ];
    }

    /**
     * @return string[] Directories found under `test-data`, relative to it
     */
    private function scan(int $depth): array
    {
        $base = realpath(TESTS_DIR . '/../test-data');
        $found = [];

        /** @var SplFileInfo $item */
        foreach (new ScanDirectoryIterator($base, $depth) as $path => $item) {
            if ($item->isDir()) {
                $found[] = substr($path, strlen($base) + 1);
            }
        }

        return $found;
    }
}
