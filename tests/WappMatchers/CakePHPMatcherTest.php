<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\MatchResult\CakePHP;
use Plesk\Wappspector\WappMatchers\CakePHPMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(CakePHPMatcher::class)]
class CakePHPMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new CakePHPMatcher();
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['cakephp/3', '3.10.5'],
            ['cakephp/4', '4.4.15'],
        ];
    }

    protected function getMatchResultClassname(): string
    {
        return CakePHP::class;
    }
}
