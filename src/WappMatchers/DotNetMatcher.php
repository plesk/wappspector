<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

class DotNetMatcher implements WappMatcherInterface
{
    private const HEX_SIGNATURE = '4d5a';

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        $result = $this->matchDotNet($fs, $path);
        if (!$result) {
            return $this->matchDotNet($fs, rtrim($path) . '/../');
        }

        return $result;
    }

    /**
     * @throws FilesystemException
     */
    public function matchDotNet(Filesystem $fs, string $path): array
    {
        $list = $fs->listContents($path);

        foreach ($list as $item) {
            /** @var StorageAttributes $item */
            if ($item->isFile() && str_ends_with($item->path(), '.dll')) {
                $handle = $fs->readStream($item->path());
                $hex = bin2hex(fread($handle, 4));
                if (str_contains($hex, self::HEX_SIGNATURE)) {
                    return [
                        'matcher' => 'dotnet',
                        'path' => $path,
                    ];
                }
            }
        }

        return [];
    }
}
