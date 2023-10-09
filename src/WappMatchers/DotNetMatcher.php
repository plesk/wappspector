<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use Plesk\Wappspector\MatchResult\DotNetMatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

class DotNetMatcher implements WappMatcherInterface
{
    use UpLevelMatcherTrait;

    private const HEX_SIGNATURE = '4d5a';

    /**
     * @throws FilesystemException
     */
    public function doMatch(Filesystem $fs, string $path): MatchResultInterface
    {
        foreach ($fs->listContents($path) as $item) {
            /** @var StorageAttributes $item */
            if (!$item->isFile() || !str_ends_with($item->path(), '.dll')) {
                continue;
            }

            $handle = $fs->readStream($item->path());
            $hex = bin2hex(fread($handle, 4));
            if (str_contains($hex, self::HEX_SIGNATURE)) {
                return new DotNetMatchResult($path);
            }
        }

        return new EmptyMatchResult();
    }
}
