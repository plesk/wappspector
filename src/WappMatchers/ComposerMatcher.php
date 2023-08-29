<?php

namespace Plesk\Wappspector\WappMatchers;

use JsonException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\Matchers;

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

        $json = [];
        try {
            $json = json_decode($fs->read($composerJsonFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // ignore composer.json errors
        }

        return [
            'matcher' => Matchers::COMPOSER,
            'path' => $path,
            'application' => $json['name'] ?? 'unknown',
            'version' => $json['version'] ?? 'dev',
        ];
    }
}
