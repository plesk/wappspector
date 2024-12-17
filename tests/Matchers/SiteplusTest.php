<?php

declare(strict_types=1);

namespace Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\Siteplus;
use Plesk\Wappspector\MatchResult\Siteplus as MatchResult;
use Test\Matchers\AbstractMatcherTestCase;

#[CoversClass(Siteplus::class)]
class SiteplusTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new Siteplus();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['siteplus/0.58.3', '0.58.3'],
            ['siteplus/0.58.7', '0.58.7'],
        ];
    }
}
