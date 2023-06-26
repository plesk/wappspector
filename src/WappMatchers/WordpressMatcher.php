<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\Matchers;

class WordpressMatcher implements WappMatcherInterface
{
    private const VERSION_FILE = 'wp-includes/version.php';

    /**
     * @throws FilesystemException
     */
    private function detectVersion(Filesystem $fs, string $path): ?string
    {
        $versionFile = rtrim($path, '/') . '/' . self::VERSION_FILE;
        preg_match("/\\\$wp_version\\s*=\\s*'([^']+)'/", $fs->read($versionFile), $matches);

        if (count($matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @throws FilesystemException
     */
    private function isWordpress(Filesystem $fs, string $path): bool
    {
        $versionFile = rtrim($path, '/') . '/' . self::VERSION_FILE;

        if (!$fs->fileExists($versionFile)) {
            return false;
        }

        $fileContents = $fs->read($versionFile);

        return stripos($fileContents, '$wp_version =') !== false;
    }

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        if (!$this->isWordpress($fs, $path)) {
            return [];
        }

        return [
            'matcher' => Matchers::WORDPRESS,
            'version' => $this->detectVersion($fs, $path),
            'path' => $path,
        ];
    }
}
