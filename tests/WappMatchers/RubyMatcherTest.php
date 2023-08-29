<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\RubyMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(RubyMatcher::class)]
class RubyMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new RubyMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::RUBY;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['ruby', null],
        ];
    }
}
