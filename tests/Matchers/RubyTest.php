<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\Ruby;
use Plesk\Wappspector\MatchResult\Ruby as MatchResult;

#[CoversClass(Ruby::class)]
class RubyTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new Ruby();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['ruby', null],
        ];
    }
}
