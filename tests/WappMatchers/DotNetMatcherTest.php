<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\MatchResult\DotNet;
use Plesk\Wappspector\WappMatchers\DotNetMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(DotNetMatcher::class)]
class DotNetMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new DotNetMatcher();
    }

    protected function getMatchResultClassname(): string
    {
        return DotNet::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['dotnet', null],
            ['dotnet/wwwroot', null],
        ];
    }
}
