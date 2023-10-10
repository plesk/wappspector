<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class DotNet extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'dotnet';
    }

    public function getName(): string
    {
        return '.NET';
    }
}
