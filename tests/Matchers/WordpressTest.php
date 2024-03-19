<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\Wordpress;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\Wordpress as MatchResult;

#[CoversClass(Wordpress::class)]
class WordpressTest extends AbstractMatcherTestCase
{
    public function testEmptyConfig(): void
    {
        $match = $this->getMatch('wordpress/emptyconfig');
        $this->assertInstanceOf(EmptyMatchResult::class, $match);
    }

    protected function getMatcherObj(): MatcherInterface
    {
        return new Wordpress();
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['wordpress/unreadableversion', null],
            ['wordpress/wordpress2.2', '2.2.1'],
            ['wordpress/wordpress2.9', '2.9'],
            ['wordpress/wordpress3.7', '3.7.5'],
            ['wordpress/wordpress4.0', '4.9.25'],
        ];
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }
}
