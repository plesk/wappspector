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
        $composerJson = $this->findComposerJson($fs, $path);
        if ($composerJson === null) {
            $composerJson = $this->findComposerJson($fs, rtrim($path, '/') . '/../');
        }

        if ($composerJson === null) {
            return [];
        }

        return [
            'matcher' => 'composer',
            'path' => $path,
            'application' => $composerJson['name'] ?? 'unknown',
            'version' => $composerJson['version'] ?? 'dev',
        ];
    }

    /**
     * @throws FilesystemException
     */
    public function findComposerJson(Filesystem $fs, string $path): ?array
    {
        $composerJsonFile = $this->getPath($path);
        if (!$fs->fileExists($composerJsonFile)) {
            return null;
        }

        return json_decode($fs->read($composerJsonFile), JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
    }

    private function getPath(string $path): string
    {
        return rtrim($path, '/') . '/composer.json';
    }
}
