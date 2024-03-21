<?php

declare(strict_types=1);


namespace Test\Matchers;

use PHPUnit\Framework\Attributes\CoversClass;
use Plesk\Wappspector\Matchers\Drupal;
use Plesk\Wappspector\Matchers\MatcherInterface;
use Plesk\Wappspector\MatchResult\Drupal as MatchResult;

#[CoversClass(Drupal::class)]
class DrupalTest extends AbstractMatcherTestCase
{
    protected function getMatcherObj(): MatcherInterface
    {
        return new Drupal();
    }

    protected function getMatchResultClassname(): string
    {
        return MatchResult::class;
    }

    public static function detectablePathsProvider(): array
    {
        return [
            ['drupal/drupal6', '6.999'],
            ['drupal/drupal7', '7.999'],
            ['drupal/drupal8', '8.999.0-beta3'],
            ['drupal/drupal9', '9.999.10'],
            ['drupal/drupal10', '10.999.1'],
            ['drupal/unreadableversion', null],
        ];
    }
}
