<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\DotNet;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\MatchResult\DotNet as MatchResult;

#[CoversClass(DotNet::class)]
class DotNetTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new DotNet();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['dotnet', null],
            ['dotnet/wwwroot', null],
        ];
    }
}
