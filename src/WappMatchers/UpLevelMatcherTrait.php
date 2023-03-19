<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

trait UpLevelMatcherTrait
{
    abstract protected function doMatch(Filesystem $fs, string $path): array;

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): array
    {
        if (!$result = $this->doMatch($fs, $path)) {
            return $this->doMatch($fs, rtrim($path) . '/../');
        }

        return $result;
    }
}
