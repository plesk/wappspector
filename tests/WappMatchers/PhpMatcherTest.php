<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\PhpMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(PhpMatcher::class)]
class PhpMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new PhpMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::PHP;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['php/direct', null],
            ['php/nested', null],
        ];
    }
}
