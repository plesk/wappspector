<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

interface MatchResultInterface
{
    public function getMatcher(): string;
    public function getPath(): string;
    public function getVersion(): ?string;
    public function getApplication(): ?string;
}
