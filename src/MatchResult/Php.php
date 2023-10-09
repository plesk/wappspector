<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

use Plesk\Wappspector\Matchers;

class Php extends AbstractMatchResult
{
    public function getMatcher(): string
    {
        return Matchers::PHP;
    }
}
