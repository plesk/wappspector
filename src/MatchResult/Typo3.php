<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

use Plesk\Wappspector\Matchers;

class Typo3 extends AbstractMatchResult
{
    public function getMatcher(): string
    {
        return Matchers::TYPO3;
    }
}
