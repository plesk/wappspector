<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToListContents;
use Plesk\Wappspector\Matchers;

class PhpMatcher implements WappMatcherInterface
{
    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        try {
            $list = $fs->listContents($path);
            foreach ($list as $item) {
                /** @var StorageAttributes $item */
                if ($item->isFile() && str_ends_with($item->path(), '.php')) {
                    return [
                        'matcher' => Matchers::PHP,
                        'path' => $path,
                        'version' => null,
                    ];
                }

                if ($item->isDir() && $item->path() === ltrim(rtrim($path, '/') . '/src', '/')) {
                    return $this->match($fs, rtrim($path, '/') . '/src');
                }
            }
        } catch (UnableToListContents) {
            // skip dir if it is inaccessible
        }

        return [];
    }
}
