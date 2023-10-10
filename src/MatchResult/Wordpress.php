<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Wordpress extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'wordpress';
    }

    public function getName(): string
    {
        return 'WordPress';
    }
}
