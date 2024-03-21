<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\Typo3;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\Typo3 as MatchResult;

#[CoversClass(Typo3::class)]
class Typo3Test extends AbstractMatcherTestCase
{
    public function testBroken(): void
    {
        $match = $this->getMatch('typo3cms/typo3_broken');
        $this->assertInstanceOf(EmptyMatchResult::class, $match);
    }

    protected function getMatcherObj(): MatcherInterface
    {
        return new Typo3();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['typo3cms/typo3_4-5', '4.999.30'],
            ['typo3cms/typo3_6-2', '6.999.10'],
            ['typo3cms/typo3_7', '7.999.32'],
            ['typo3cms/typo3_8', '8.999.32'],
            ['typo3cms/typo3_9', '9.999.31'],
            ['typo3cms/typo3_10', '10.999.37'],
            ['typo3cms/typo3_11', '11.999.30'],
            ['typo3cms/typo3_12', '12.999.4'],
        ];
    }
}
