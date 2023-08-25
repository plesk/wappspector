<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\LaravelMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(LaravelMatcher::class)]
class LaravelMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new LaravelMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::LARAVEL;
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
