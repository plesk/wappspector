<?php

declare(strict_types=1);


namespace Plesk\Wappspector\MatchResult;

use JsonSerializable;
use League\Flysystem\PathTraversalDetected;
use League\Flysystem\WhitespacePathNormalizer;

class MatchResult implements MatchResultInterface, JsonSerializable
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

    public static function createById(
        string $id,
        ?string $path = null,
        ?string $version = null,
        ?string $application = null
    ): MatchResultInterface {
        $classname = match ($id) {
            CakePHP::ID => CakePHP::class,
            CodeIgniter::ID => CodeIgniter::class,
            Composer::ID => Composer::class,
            DotNet::ID => DotNet::class,
            Drupal::ID => Drupal::class,
            Joomla::ID => Joomla::class,
            Laravel::ID => Laravel::class,
            NodeJs::ID => NodeJs::class,
            Php::ID => Php::class,
            Prestashop::ID => Prestashop::class,
            Python::ID => Python::class,
            Ruby::ID => Ruby::class,
            Symfony::ID => Symfony::class,
            Typo3::ID => Typo3::class,
            Wordpress::ID => Wordpress::class,
            Yii::ID => Yii::class,
            Sitejet::ID => Sitejet::class,
            WebPresenceBuilder::ID => WebPresenceBuilder::class,
            Sitepro::ID => Sitepro::class,
            Duda::ID => Duda::class,
            Siteplus::ID => Siteplus::class,
            default => null,
        };

        if (!$classname) {
            return new EmptyMatchResult();
        }

        return new $classname(path: $path ?? '', version: $version, application: $application);
    }
}
