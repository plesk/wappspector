<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\Laravel;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\MatchResult\Laravel as MatchResult;

#[CoversClass(Laravel::class)]
class LaravelTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new Laravel();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['laravel/laravel9', '9.19'],
            ['laravel/laravel10', '10.17.1'],
            ['laravel/broken', null],
        ];
    }
}
