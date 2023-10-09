<?php

declare(strict_types=1);

namespace Test\Matchers;

use League\Flysystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\FileSystemFactory;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

#[CoversClass(FileSystemFactory::class)]
abstract class AbstractMatcherTestCase extends TestCase
{
    protected function getFsObject(): Filesystem
    {
        return (new FileSystemFactory())(TESTS_DIR . '/../test-data');
    }

    #[DataProvider('detectablePathsProvider')]
    public function testMatch(string $path, ?string $version): void
    {
        $result = $this->getMatch($path);

        $this->assertInstanceOf($this->getMatchResultClassname(), $result);
        $this->assertEquals($result->getVersion(), $version);
    }

    abstract protected function getMatcherObj(): MatcherInterface;

    abstract protected function getMatchResultClassname(): string;

    /**
     * @return array Each element is an array with two elements: 1. path inside `test-data` 2. version
     */
    abstract public static function detectablePathsProvider(): array;

    protected function getMatch(string $path): MatchResultInterface
    {
        return $this->getMatcherObj()->match($this->getFsObject(), $path);
    }
}
