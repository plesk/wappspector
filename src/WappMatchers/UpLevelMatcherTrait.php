<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\PathTraversalDetected;
use League\Flysystem\UnableToListContents;
use League\Flysystem\WhitespacePathNormalizer;

trait UpLevelMatcherTrait
{
    abstract protected function doMatch(Filesystem $fs, string $path): array;

    public function match(Filesystem $fs, string $path): array
    {
        $matcher = $this->safeScanDir($fs, $path) ?: $this->safeScanDir($fs, rtrim($path) . '/../');
        return $this->normalizeMatcherPath($matcher);
    }

    private function safeScanDir(Filesystem $fs, string $path): array
    {
        $result = [];
        try {
            $result = $this->doMatch($fs, $path);
        } catch (UnableToListContents | PathTraversalDetected) {
//             skip dir if it is inaccessible
        }

        return $result;
    }

    private function normalizeMatcherPath(array $matcher): array
    {
        if (array_key_exists('path', $matcher)) {
            $matcher['path'] = (new WhitespacePathNormalizer())->normalizePath($matcher['path']);
        }

        return $matcher;
    }
}
