<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class ComposerMatcher implements WappMatcherInterface
{
    use UpLevelMatcherTrait;

    private function getPath(string $path): string
    {
        return rtrim($path, '/') . '/composer.json';
    }

    /**
     * @throws FilesystemException
     */
    protected function doMatch(Filesystem $fs, string $path): array
    {
        $composerJsonFile = $this->getPath($path);
        if (!$fs->fileExists($composerJsonFile)) {
            return [];
        }

        $composerJson = json_decode($fs->read($composerJsonFile), JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);

        return [
            'matcher' => 'composer',
            'path' => $path,
            'application' => $composerJson['name'] ?? 'unknown',
            'version' => $composerJson['version'] ?? 'dev',
        ];
    }
}
