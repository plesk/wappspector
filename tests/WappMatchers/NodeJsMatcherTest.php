<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\MatchResult\NodeJs;
use Plesk\Wappspector\WappMatchers\NodeJsMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(NodeJsMatcher::class)]
class NodeJsMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new NodeJsMatcher();
    }

    protected function getMatchResultClassname(): string
    {
        return NodeJs::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['nodejs', null],
            ['nodejs/broken', null],
        ];
    }
}
