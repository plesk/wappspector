<?php

namespace Plesk\Wappspector\Matchers;

use JsonException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\Composer as MatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

class Composer implements MatcherInterface
{
    use UpLevelMatcherTrait;

    private function getPath(string $path): string
    {
        return rtrim($path, '/') . '/composer.json';
    }

    /**
     * @throws FilesystemException
     */
    protected function doMatch(Filesystem $fs, string $path): MatchResultInterface
    {
        $composerJsonFile = $this->getPath($path);
        if (!$fs->fileExists($composerJsonFile)) {
            return new EmptyMatchResult();
        }

        $json = [];
        try {
            $json = json_decode($fs->read($composerJsonFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            // ignore composer.json errors
        }

        return new MatchResult($path, $json['version'] ?? 'dev', $json['name'] ?? 'unknown');
    }
}
