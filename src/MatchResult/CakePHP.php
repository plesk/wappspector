<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class CakePHP extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'cakephp';
    }

    public function getName(): string
    {
        return 'CakePHP';
    }
}
