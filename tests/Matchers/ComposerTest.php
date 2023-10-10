<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\Composer;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\MatchResult\Composer as MatchResult;

#[CoversClass(Composer::class)]
class ComposerTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new Composer();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['composer', '0.0.1'],
            ['composer/broken', 'dev'],
        ];
    }
}
