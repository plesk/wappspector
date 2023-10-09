<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

use JsonSerializable;
use League\Flysystem\PathTraversalDetected;
use League\Flysystem\WhitespacePathNormalizer;

abstract class AbstractMatchResult implements MatchResultInterface, JsonSerializable
{
    public function __construct(
        protected string $path,
        protected ?string $version = null,
        protected ?string $application = null,
    ) {
        try {
            $this->path = (new WhitespacePathNormalizer())->normalizePath($this->path);
        } catch (PathTraversalDetected) {
            $this->path = '/';
        }
    }


    public function getPath(): string
    {
        return $this->path;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getApplication(): ?string
    {
        return $this->application;
    }

    public function jsonSerialize(): array
    {
        return [
            'matcher' => $this->getMatcher(),
            'path' => $this->getPath(),
            'version' => $this->getVersion(),
            'application' => $this->getApplication(),
        ];
    }
}
