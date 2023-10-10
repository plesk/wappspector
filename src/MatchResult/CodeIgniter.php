<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class CodeIgniter extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'codeigniter';
    }

    public function getName(): string
    {
        return 'CodeIgniter';
    }
}
