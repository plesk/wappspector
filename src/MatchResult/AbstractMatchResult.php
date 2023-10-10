<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

use JsonSerializable;
use League\Flysystem\PathTraversalDetected;
use League\Flysystem\WhitespacePathNormalizer;

abstract class AbstractMatchResult implements MatchResultInterface, JsonSerializable
{
    public const ID = null;
    public const NAME = null;

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

    public function getId(): string
    {
        return static::ID;
    }

    public function getName(): string
    {
        return static::NAME;
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'path' => $this->getPath(),
            'version' => $this->getVersion(),
            'application' => $this->getApplication(),
        ];
    }
}
