<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

class PhpMatcher implements WappMatcherInterface
{
    public function __construct(private readonly Filesystem $fs)
    {
    }

    /**
     * @throws FilesystemException
     */
    public function match(string $path): iterable
    {
        $result = [];
        $list = $this->fs->listContents($path);
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
                    ...$this->match(rtrim($path, '/') . '/src'),
                ];
            }
        }
        return $result;
    }
}
