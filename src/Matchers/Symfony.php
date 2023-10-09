<?php

declare(strict_types=1);


namespace Plesk\Wappspector\Matchers;

use JsonException;
use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Symfony as MatchResult;

class Symfony implements MatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $symfonyLockFile = rtrim($path, '/') . '/symfony.lock';

        if (!$fs->fileExists($symfonyLockFile)) {
            return new EmptyMatchResult();
        }

        $json = [];
        try {
            $json = json_decode($fs->read($symfonyLockFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            // ignore symfony.lock errors
        }

        return new MatchResult($path, $json["symfony/framework-bundle"]["version"] ?? null);
    }
}
