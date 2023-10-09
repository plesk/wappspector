<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class NodeJs extends AbstractMatchResult
{
    public function getId(): string
    {
        return 'nodejs';
    }

    public function getName(): string
    {
        return 'Node.js';
    }
}
