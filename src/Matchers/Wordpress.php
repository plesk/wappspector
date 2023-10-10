<?php

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Wordpress as MatchResult;

class Wordpress implements MatcherInterface
{
    private const VERSION_FILE = 'wp-includes/version.php';

    /**
     * @throws FilesystemException
     */
    private function detectVersion(Filesystem $fs, string $path): ?string
    {
        $versionFile = rtrim($path, '/') . '/' . self::VERSION_FILE;
        preg_match("/\\\$wp_version\\s*=\\s*'([^']+)'/", $fs->read($versionFile), $matches);

        if ($matches !== []) {
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
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        if (!$this->isWordpress($fs, $path)) {
            return new EmptyMatchResult();
        }

        return new MatchResult($path, $this->detectVersion($fs, $path));
    }
}
