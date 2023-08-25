<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\CodeIgniterMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(CodeIgniterMatcher::class)]
class CodeIgniterMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new CodeIgniterMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::CODEIGNITER;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['codeigniter/4', '4.3.6'],
        ];
    }
}
