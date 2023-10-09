<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\WappMatchers\Typo3Matcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(Typo3Matcher::class)]
class Typo3MatcherTest extends AbstractMatcherTestCase
{
    public function testBroken(): void
    {
        $match = $this->getMatch('typo3cms/typo3_broken');
        $this->assertInstanceOf(EmptyMatchResult::class, $match);
    }

    protected function getMatcherObj(): WappMatcherInterface
    {
        return new Typo3Matcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::TYPO3;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['typo3cms/typo3_4-5', '4.5.30'],
            ['typo3cms/typo3_6-2', '6.2.10'],
            ['typo3cms/typo3_7', '7.6.32'],
            ['typo3cms/typo3_8', '8.7.32'],
            ['typo3cms/typo3_9', '9.5.31'],
            ['typo3cms/typo3_10', '10.4.37'],
            ['typo3cms/typo3_11', '11.5.30'],
            ['typo3cms/typo3_12', '12.4.4'],
        ];
    }
}
