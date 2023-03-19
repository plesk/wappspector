<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

class PhpMatcher implements WappMatcherInterface
{
    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        $list = $fs->listContents($path);
        foreach ($list as $item) {
            /** @var StorageAttributes $item */
            if ($item->isFile() && str_ends_with($item->path(), '.php')) {
                return [
                    'matcher' => 'php',
                    'path' => $path,
                ];
            }

            if ($item->isDir() && $item->path() === 'src') {
                return $this->match($fs, rtrim($path, '/') . '/src');
            }
        }

        return [];
    }
}
