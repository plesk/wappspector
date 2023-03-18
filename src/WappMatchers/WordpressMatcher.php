<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class WordpressMatcher implements WappMatcherInterface
{
    public function __construct(private readonly Filesystem $fs)
    {
    }

    public function detectVersion(string $contents): ?string
    {
        preg_match("/\\\$wp_version\\s*=\\s*'([^']+)'/", $contents, $matches);

        if (count($matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @throws FilesystemException
     */
    public function match(string $path): iterable
    {
        $result = [];
        $list = $this->fs->listContents($path);

        foreach ($list as $item) {
            $currPath = $item->path();

            if (!$item->isFile()) {
                continue;
            }

            if (basename($currPath) !== "version.php") {
                continue;
            }

            $fileContents = $this->fs->read($currPath);

            if (stripos($fileContents, '$wp_version =') !== false) {
                $version = $this->detectVersion($fileContents);

                $result[] = [
                    'matcher' => 'wordpress',
                    'version' => $version,
                    'path' => $path,
                ];
                break;
            }
        }

        return $result;
    }
}
