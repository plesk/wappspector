<?php

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use Plesk\Wappspector\MatchResult\DotNet as MatchResult;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

class DotNet implements MatcherInterface
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
                return new MatchResult($path);
            }
        }

        return new EmptyMatchResult();
    }
}
