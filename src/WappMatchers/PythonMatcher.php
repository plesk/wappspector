<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

class PythonMatcher implements WappMatcherInterface
{
    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
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
