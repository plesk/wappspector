<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Symfony extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'symfony';
    }

    public function getName(): string
    {
        return 'Symfony';
    }
}
