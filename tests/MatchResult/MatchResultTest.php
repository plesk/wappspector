<?php

declare(strict_types=1);


namespace Test\MatchResult;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResult;
use Plesk\Wappspector\MatchResult\Php;
use Plesk\Wappspector\MatchResult\Python;
use Plesk\Wappspector\MatchResult\Wordpress;

#[CoversClass(MatchResult::class)]
class MatchResultTest extends TestCase
{
    /**
     * @dataProvider pathDataProvider
     */
    public function testParentDirNormalization($originalPath, $normalizedPath): void
    {
        $matchResult = new class ($originalPath) extends MatchResult {
            public function getId(): string
            {
                return 'mock';
            }

            public function getName(): string
            {
                return 'Mock matcher';
            }
        };

        $this->assertEquals($normalizedPath, $matchResult->getPath());
    }

    public static function pathDataProvider(): array
    {
        return [
            // relative paths
            ['parent/child/..', 'parent'],
            ['parent/../child', 'child'],
            ['parent/../', ''],
            ['parent/../../', '/'],
            // absolute paths
            ['/parent/child/..', 'parent'],
            ['/parent/../child', 'child'],
            ['/parent/../', ''],
            ['/parent/../../', '/'],
        ];
    }

    public function testFactoryMethodEmptyResult(): void
    {
        $this->assertInstanceOf(EmptyMatchResult::class, MatchResult::createById('someunknownid'));
    }

    /**
     * @dataProvider idsProvider
     */
    public function testFactoryMethod(string $id, string $classname): void
    {
        $this->assertInstanceOf($classname, MatchResult::createById($id));
    }

    public static function idsProvider(): array
    {
        return [
            [Wordpress::ID, Wordpress::class],
            [Php::ID, Php::class],
            [Python::ID, Python::class],
        ];
    }

    public function testFactoryMethodArgs(): void
    {
        $args = [
            'path' => 'var/www',
            'version' => '1.2.3',
            'application' => 'My App',
        ];
        $result = MatchResult::createById('php', ...$args);
        $this->assertInstanceOf(Php::class, $result);
        $this->assertEquals($args['path'], $result->getPath());
        $this->assertEquals($args['version'], $result->getVersion());
        $this->assertEquals($args['application'], $result->getApplication());
    }
}
