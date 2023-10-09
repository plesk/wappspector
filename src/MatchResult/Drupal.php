<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

use Plesk\Wappspector\Matchers;

class Drupal extends AbstractMatchResult
{
    public function getMatcher(): string
    {
        return Matchers::DRUPAL;
    }
}
