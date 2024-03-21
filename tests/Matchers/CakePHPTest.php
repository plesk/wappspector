<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\CakePHP;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\MatchResult\CakePHP as MatchResult;

#[CoversClass(CakePHP::class)]
class CakePHPTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new CakePHP();
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['cakephp/3', '3.999.5'],
            ['cakephp/4', '4.999.15'],
        ];
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }
}
