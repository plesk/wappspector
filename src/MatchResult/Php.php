<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Php extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'php';
    }

    public function getName(): string
    {
        return 'PHP';
    }
}
