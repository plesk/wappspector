<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\NodeJs;
use Plesk\Wappspector\MatchResult\NodeJs as MatchResult;

#[CoversClass(NodeJs::class)]
class NodeJsTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new NodeJs();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['nodejs', null],
            ['nodejs/broken', null],
        ];
    }
}
