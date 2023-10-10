<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Laravel extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'laravel';
    }

    public function getName(): string
    {
        return 'Laravel';
    }
}
