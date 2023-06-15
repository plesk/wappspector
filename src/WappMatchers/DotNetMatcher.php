<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use Plesk\Wappspector\Matchers;

class DotNetMatcher implements WappMatcherInterface
{
    use UpLevelMatcherTrait;

    private const HEX_SIGNATURE = '4d5a';
    
    /**
     * @throws FilesystemException
     */
    public function doMatch(Filesystem $fs, string $path): array
    {
        $list = $fs->listContents($path);

        foreach ($list as $item) {
            /** @var StorageAttributes $item */
            if ($item->isFile() && str_ends_with($item->path(), '.dll')) {
                $handle = $fs->readStream($item->path());
                $hex = bin2hex(fread($handle, 4));
                if (str_contains($hex, self::HEX_SIGNATURE)) {
                    return [
                        'matcher' => Matchers::DOTNET,
                        'path' => $path,
                    ];
                }
            }
        }

        return [];
    }
}
