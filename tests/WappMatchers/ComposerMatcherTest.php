<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\MatchResult\Composer;
use Plesk\Wappspector\WappMatchers\ComposerMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(ComposerMatcher::class)]
class ComposerMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new ComposerMatcher();
    }

    protected function getMatchResultClassname(): string
    {
        return Composer::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['composer', '0.0.1'],
            ['composer/broken', 'dev'],
        ];
    }
}
