<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

class EmptyMatchResult implements MatchResultInterface
{

    public function getPath(): string
    {
        return '';
    }

    public function getVersion(): ?string
    {
        return null;
    }

    public function getApplication(): ?string
    {
        return null;
    }
}
