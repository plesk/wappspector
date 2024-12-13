<?php

declare(strict_types=1);


namespace Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\WebPresenceBuilder;
use Plesk\Wappspector\MatchResult\WebPresenceBuilder as MatchResult;
use Test\Matchers\AbstractMatcherTestCase;

#[CoversClass(WebPresenceBuilder::class)]
class WebPresenceBuilderTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new WebPresenceBuilder();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['webpresencebuilder/meta', null],
            ['webpresencebuilder/dom', null],
        ];
    }
}
