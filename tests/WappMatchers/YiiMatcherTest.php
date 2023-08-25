<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;
use Plesk\Wappspector\WappMatchers\YiiMatcher;

#[CoversClass(YiiMatcher::class)]
class YiiMatcherTest extends AbstractMatcherTestCase
{
    public static function detectablePathsProvider(): array
    {
        return [
            ['yii/2', '2.0.48.1'],
        ];
    }

    protected function getMatcherObj(): WappMatcherInterface
    {
        return new YiiMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::YII;
    }
}
