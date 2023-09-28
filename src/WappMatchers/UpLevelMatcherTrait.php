<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\UnableToListContents;

trait UpLevelMatcherTrait
{
    abstract protected function doMatch(Filesystem $fs, string $path): array;

    public function match(Filesystem $fs, string $path): array
    {
        return $this->safeScanDir($fs, $path) ?: $this->safeScanDir($fs, rtrim($path) . '/../');
    }

    private function safeScanDir(Filesystem $fs, string $path): array
    {
        $result = [];
        try {
            $result = $this->doMatch($fs, $path);
        } catch (UnableToListContents) {
            // skip dir if it is inaccessible
        }

        return $result;
    }
}
