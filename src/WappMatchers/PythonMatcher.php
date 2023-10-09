<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\PythonMatchResult;

class PythonMatcher implements WappMatcherInterface
{
    use UpLevelMatcherTrait;

    protected function doMatch(Filesystem $fs, string $path): MatchResultInterface
    {
        foreach ($fs->listContents($path) as $item) {
            /** @var StorageAttributes $item */
            if ($item->isFile() && str_ends_with($item->path(), '.py')) {
                return new PythonMatchResult($path);
            }
        }

        return new EmptyMatchResult();
    }
}
