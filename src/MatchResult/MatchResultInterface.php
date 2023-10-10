<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

interface MatchResultInterface
{
    /**
     * Internal ID of the technology
     */
    public function getId(): string;

    /**
     * Human-readable technology name
     */
    public function getName(): string;

    public function getPath(): string;

    public function getVersion(): ?string;

    public function getApplication(): ?string;
}
