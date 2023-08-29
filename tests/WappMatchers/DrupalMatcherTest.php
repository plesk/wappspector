<?php

declare(strict_types=1);


namespace Test\WappMatchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\WappMatchers\DrupalMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

#[CoversClass(DrupalMatcher::class)]
class DrupalMatcherTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): WappMatcherInterface
    {
        return new DrupalMatcher();
    }

    protected function getMatcherName(): string
    {
        return Matchers::DRUPAL;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['drupal/drupal6', '6.34'],
            ['drupal/drupal7', '7.33'],
            ['drupal/drupal8', '8.0.0-beta3'],
            ['drupal/drupal9', '9.5.10'],
            ['drupal/drupal10', '10.1.1'],
            ['drupal/unreadableversion', null],
        ];
    }
}
