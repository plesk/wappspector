<?php

declare(strict_types=1);


namespace Test\MatchResult;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Plesk\Wappspector\MatchResult\AbstractMatchResult;

#[CoversClass(AbstractMatchResult::class)]
class MatchResultTest extends TestCase
{

    /**
     * @dataProvider pathDataProvider
     */
    public function testParentDirNormalization($originalPath, $normalizedPath): void
    {
        $matchResult = new class ($originalPath) extends AbstractMatchResult {
            public function getMatcher(): string
            {
                return '';
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
}
