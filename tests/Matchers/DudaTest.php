<?php

declare(strict_types=1);


namespace Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\Matchers\Duda;
use Plesk\Wappspector\MatchResult\Duda as MatchResult;
use Test\Matchers\AbstractMatcherTestCase;

#[CoversClass(Duda::class)]
class DudaTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new Duda();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['duda/css-desktop-dmDudaonePreviewBody', null],
            ['duda/css-desktop-dudaSnipcartProductGalleryId', null],
            ['duda/css-mobile-dmDudaonePreviewBody', null],
            ['duda/css-tablet-dmDudaonePreviewBody', null],
            ['duda/js', null],
        ];
    }
}
