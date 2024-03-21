<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\Prestashop;
use Plesk\Wappspector\MatchResult\Prestashop as MatchResult;

#[CoversClass(Prestashop::class)]
class PrestashopTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new Prestashop();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['prestashop/prestashop1.6', '1.999.0.14'],
        ];
    }
}
