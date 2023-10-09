<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Python extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'python';
    }

    public function getName(): string
    {
        return 'Python';
    }
}
