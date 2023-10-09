<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\PhpMatchResult;

class PhpMatcher implements WappMatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        try {
            $list = $fs->listContents($path);
            foreach ($list as $item) {
                /** @var StorageAttributes $item */
                if ($item->isFile() && str_ends_with($item->path(), '.php')) {
                    return new PhpMatchResult($path);
                }

                if ($item->isDir() && $item->path() === ltrim(rtrim($path, '/') . '/src', '/')) {
                    return $this->match($fs, rtrim($path, '/') . '/src');
                }
            }
        } catch (FilesystemException) {
            // skip dir if it is inaccessible
        }

        return new EmptyMatchResult();
    }
}
