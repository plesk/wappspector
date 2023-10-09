<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\MatchResult\Python;
use Plesk\Wappspector\WappMatchers\PythonMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(PythonMatcher::class)]
class PythonMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new PythonMatcher();
    }

    protected function getMatchResultClassname(): string
    {
        return Python::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['python', null],
            ['python/nested', null],
        ];
    }
}
