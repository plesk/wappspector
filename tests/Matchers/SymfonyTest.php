<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\Symfony;
use Plesk\Wappspector\MatchResult\Symfony as MatchResult;

#[CoversClass(Symfony::class)]
class SymfonyTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new Symfony();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['symfony/5.4', '5.999'],
            ['symfony/6.3', '6.999'],
            ['symfony/broken', null],
        ];
    }
}
