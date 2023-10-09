<?php

declare(strict_types=1);


namespace Plesk\Wappspector\WappMatchers;

use JsonException;
use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\SymfonyMatchResult;

class SymfonyMatcher implements WappMatcherInterface
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
        } catch (JsonException $e) {
            // ignore symfony.lock errors
        }

        return new SymfonyMatchResult($path, $json["symfony/framework-bundle"]["version"] ?? null);
    }
}
