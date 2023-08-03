<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToListContents;

trait UpLevelMatcherTrait
{
    abstract protected function doMatch(Filesystem $fs, string $path): array;

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): array
    {
        if (!$result = $this->doMatch($fs, $path)) {
            try {
                $result = $this->doMatch($fs, rtrim($path) . '/../');
            } catch (UnableToListContents $e) {
                // skip parent dir if it is inaccessible
            }
        }

        return $result;
    }
}
