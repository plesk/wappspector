<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Typo3 extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'typo3';
    }

    public function getName(): string
    {
        return 'TYPO3';
    }
}
