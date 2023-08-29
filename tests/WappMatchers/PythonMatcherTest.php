<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\PythonMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(PythonMatcher::class)]
class PythonMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new PythonMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::PYTHON;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['python', null],
        ];
    }
}
