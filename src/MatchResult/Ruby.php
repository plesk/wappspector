<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Ruby extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'ruby';
    }

    public function getName(): string
    {
        return 'Ruby';
    }
}
