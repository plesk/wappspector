<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\YiiMatcher;
use Plesk\Wappspector\MatchResult\Yii as MatchResult;

#[CoversClass(YiiMatcher::class)]
class YiiTest extends AbstractMatcherTestCase
{
    public static function detectablePathsProvider(): array
    {
        return [
            ['yii/2', '2.0.48.1'],
        ];
    }

    protected function getMatcherObj(): MatcherInterface
    {
        return new YiiMatcher();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }
}