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
        $result = [];
        $list = $fs->listContents($path);
        foreach ($list as $item) {
            /** @var StorageAttributes $item */
            if ($item->isFile() && str_ends_with($item->path(), '.php')) {
                $result[] = [
                    'matcher' => 'php',
                    'path' => $path,
                ];
                break;
            }
            if ($item->isDir() && $item->path() === 'src') {
                $result = [
                    ...$result,
                    ...$this->match($fs, rtrim($path, '/') . '/src'),
                ];
            }
        }
        return $result;
    }
}
