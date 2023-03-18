<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;

class ComposerMatcher implements WappMatcherInterface
{
    public function __construct(private readonly Filesystem $fs)
    {
    }

    public function match(string $path): iterable
    {
        $result = [];
        $composerJsonFile = rtrim($path, '/') . '/composer.json';
        if (!$this->fs->fileExists($composerJsonFile)) {
            return $result;
        }
        $composerJson = json_decode($this->fs->read($composerJsonFile), JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
        $result[] = [
            'matcher' => 'composer',
            'path' => $path,
            'application' => $composerJson['name'] ?? 'unknown',
            'version' => $composerJson['version'] ?? 'dev',
        ];
        return $result;
    }
}
