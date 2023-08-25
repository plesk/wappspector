<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;
use Plesk\Wappspector\WappMatchers\WordpressMatcher;

#[CoversClass(WordpressMatcher::class)]
class WordpressMatcherTest extends AbstractMatcherTestCase
{
    public function testEmptyConfig(): void
    {
        $match = $this->getMatch('wordpress/emptyconfig');
        $this->assertIsArray($match);
        $this->assertEmpty($match);
    }

    protected function getMatcherObj(): WappMatcherInterface
    {
        return new WordpressMatcher();
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['wordpress/unreadableversion', null],
            ['wordpress/wordpress2.2', '2.2.1'],
            ['wordpress/wordpress2.9', '2.9'],
            ['wordpress/wordpress3.7', '3.7.5'],
            ['wordpress/wordpress4.0', '4.0'],
        ];
    }

    protected function getMatcherName(): string
    {
        return Matchers::WORDPRESS;
    }
}
