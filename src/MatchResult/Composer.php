<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Composer extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'composer';
    }

    public function getName(): string
    {
        return 'Composer';
    }
}
