<?php

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Python as MatchResult;

class Python implements MatcherInterface
{
    use UpLevelMatcherTrait;

    protected function doMatch(Filesystem $fs, string $path): MatchResultInterface
    {
        foreach ($fs->listContents($path) as $item) {
            /** @var StorageAttributes $item */
            if ($item->isFile() && str_ends_with($item->path(), '.py')) {
                return new MatchResult($path);
            }
        }

        return new EmptyMatchResult();
    }
}
