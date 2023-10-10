<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class Joomla extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'joomla';
    }

    public function getName(): string
    {
        return 'Joomla!';
    }
}
