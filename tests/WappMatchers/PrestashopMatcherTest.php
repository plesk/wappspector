<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\PrestashopMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(PrestashopMatcher::class)]
class PrestashopMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new PrestashopMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::PRESTASHOP;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['prestashop/prestashop1.6', '1.6.0.14'],
        ];
    }
}
