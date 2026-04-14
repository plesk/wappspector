<?php

declare(strict_types=1);
namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\EmDash;
use Plesk\Wappspector\MatchResult\EmDash as MatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;

#[CoversClass(EmDash::class)]
class EmDashTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new EmDash();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['emdash', null],
        ];
    }

    public function testBrokenPackageJson(): void
    {
        $result = $this->getMatch('emdash/broken');
        $this->assertInstanceOf(EmptyMatchResult::class, $result);
    }
}
