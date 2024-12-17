<?php

declare(strict_types=1);


namespace Test\MatchResult;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResult;
use Plesk\Wappspector\MatchResult\Php;
use Plesk\Wappspector\MatchResult\Python;
use Plesk\Wappspector\MatchResult\Wordpress;

#[CoversClass(MatchResult::class)]
class MatchResultTest extends TestCase
{
    #[DataProvider('pathDataProvider')]
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

    #[DataProvider('idsProvider')]
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

    #[DataProvider('createByIdProvider')]
    public function testCreateById(string $matchResultClass): void
    {
        $this->assertTrue(get_class(MatchResult::createById($matchResultClass::ID)) !== EmptyMatchResult::class, 'MatchResult ' . $matchResultClass . ' is not declared in MatchResult::createById');
    }

    public static function createByIdProvider(): array
    {
        // get all PHP files in the directory
        $files = glob(__DIR__ . '/../../src/MatchResult/*.php');

        foreach ($files as $file) {
            // require the file to load its classes to have it in get_declared_classes() result
            require_once $file;
        }

        $classes = get_declared_classes();
        // get all classes that implements MatchResultInterface except for EmptyMatchResult
        $matchResultClasses = array_filter($classes, function ($class) {
            return in_array('Plesk\Wappspector\MatchResult\MatchResultInterface', class_implements($class))
                   && !in_array($class, [EmptyMatchResult::class, MatchResult::class]);
        });

        $result = [];
        foreach ($matchResultClasses as $class) {
            $result[$class] = [$class];
        }

        return $result;
    }
}
