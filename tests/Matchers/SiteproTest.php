<?php

declare(strict_types=1);


namespace Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\Sitepro;
use Plesk\Wappspector\MatchResult\Sitepro as MatchResult;
use Test\Matchers\AbstractMatcherTestCase;

#[CoversClass(Sitepro::class)]
class SiteproTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new Sitepro();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['sitepro/htaccess', null],
            ['sitepro/webconfig', null],
        ];
    }
}
