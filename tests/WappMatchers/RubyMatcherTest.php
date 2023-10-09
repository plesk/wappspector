<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\MatchResult\Ruby;
use Plesk\Wappspector\WappMatchers\RubyMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(RubyMatcher::class)]
class RubyMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new RubyMatcher();
    }

    protected function getMatchResultClassname(): string
    {
        return Ruby::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['ruby', null],
        ];
    }
}
