<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

use Plesk\Wappspector\Matchers;

class EmptyMatchResult implements MatchResultInterface
{
    public function getMatcher(): string
    {
        return Matchers::UNKNOWN;
    }

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
