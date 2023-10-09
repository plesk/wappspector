<?php

declare(strict_types=1);

namespace Test\WappMatchers;

use League\Flysystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\FileSystemFactory;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

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

        $this->assertInstanceOf(MatchResultInterface::class, $result);
        $this->assertEquals($result->getMatcher(), $this->getMatcherName());
        $this->assertEquals($result->getVersion(), $version);
    }

    abstract protected function getMatcherObj(): WappMatcherInterface;

    abstract protected function getMatcherName(): string;

    /**
     * @return array Each element is an array with two elements: 1. path inside `test-data` 2. version
     */
    abstract public static function detectablePathsProvider(): array;

    protected function getMatch(string $path): MatchResultInterface
    {
        return $this->getMatcherObj()->match($this->getFsObject(), $path);
    }
}
