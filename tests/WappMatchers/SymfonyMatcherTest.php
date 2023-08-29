<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\SymfonyMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(SymfonyMatcher::class)]
class SymfonyMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new SymfonyMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::SYMFONY;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['symfony/5.4','5.4'],
            ['symfony/6.3','6.3'],
            ['symfony/broken', null],
        ];
    }
}
