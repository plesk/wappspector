<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

class PythonMatcher implements WappMatcherInterface
{
    use UpLevelMatcherTrait;

    /**
     * @throws FilesystemException
     */
    protected function doMatch(Filesystem $fs, string $path): array
    {
        $list = $fs->listContents($path);
        foreach ($list as $item) {
            /** @var StorageAttributes $item */
            if ($item->isFile() && str_ends_with($item->path(), '.py')) {
                return [
                    'matcher' => 'python',
                    'path' => $path,
                ];
            }
        }

        return [];
    }
}
