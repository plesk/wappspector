<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class ComposerMatcher implements WappMatcherInterface
{
    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        $result = [];
        $composerJsonFile = rtrim($path, '/') . '/composer.json';
        if (!$fs->fileExists($composerJsonFile)) {
            return $result;
        }
        $composerJson = json_decode($fs->read($composerJsonFile), JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
        $result[] = [
            'matcher' => 'composer',
            'path' => $path,
            'application' => $composerJson['name'] ?? 'unknown',
            'version' => $composerJson['version'] ?? 'dev',
        ];
        return $result;
    }
}
