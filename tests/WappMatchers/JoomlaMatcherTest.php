<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\WappMatchers\JoomlaMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(JoomlaMatcher::class)]
class JoomlaMatcherTest extends AbstractMatcherTestCase
{
    public function testUnitTestConfigClass(): void
    {
        $match = $this->getMatch('joomla/unittestconfig');
        $this->assertInstanceOf(EmptyMatchResult::class, $match);
    }

    protected function getMatcherObj(): WappMatcherInterface
    {
        return new JoomlaMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::JOOMLA;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['joomla/joomla1.0', '1.0.11'],
            ['joomla/joomla1.5', '1.5.25'],
            ['joomla/joomla1.6', '1.6.6'],
            ['joomla/joomla1.7', '1.7.3'],
            ['joomla/joomla2.5', '2.5.24'],
            ['joomla/joomla3.0', '3.0.5'],
            ['joomla/joomla3.1', '3.1.1'],
            ['joomla/joomla3.2', '3.2.0'],
            ['joomla/joomla3.3', '3.3.4'],
            ['joomla/joomla3.4', '3.4.8'],
            ['joomla/joomla3.5', '3.5.1'],
            ['joomla/joomla3.8', '3.8.0'],
            ['joomla/joomla4.0', '4.0.0'],
            ['joomla/unreadableversion', null],
        ];
    }
}
