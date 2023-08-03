<?php

declare(strict_types=1);


namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\Matchers;

class SymfonyMatcher implements WappMatcherInterface
{

    public function match(Filesystem $fs, string $path): iterable
    {

        $symfonyLockFile = rtrim($path, '/') . '/symfony.lock';

        if (!$fs->fileExists($symfonyLockFile)) {
            return [];
        }

        $json = [];
        try {
            $json = json_decode($fs->read($symfonyLockFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            // ignore symfony.lock errors
        }

        return [
            'matcher' => Matchers::SYMFONY,
            'path' => $path,
            'version' => $json["symfony/framework-bundle"]["version"] ?? null,
        ];
    }
}
