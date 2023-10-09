<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\PrestashopMatchResult;

class PrestashopMatcher implements WappMatcherInterface
{
    protected const VERSIONS = [
        [
            'filename' => '/config/settings.inc.php',
            'regexp' => '/define\\(\'_PS_VERSION_\', \'(.+)\'\\)/',
        ],
    ];

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        foreach (self::VERSIONS as $version) {
            $versionFile = rtrim($path, '/') . '/' . $version['filename'];

            if (!$fs->fileExists($versionFile)) {
                continue;
            }

            return new PrestashopMatchResult($path, $this->getVersion($version, $fs, $versionFile));
        }

        return new EmptyMatchResult();
    }

    /**
     * @param array $version
     * @param Filesystem $fs
     * @param string $versionFile
     */
    public function getVersion(array $version, Filesystem $fs, string $versionFile): ?string
    {
        $result = null;
        try {
            if (preg_match($version['regexp'], $fs->read($versionFile), $matches) && count($matches) > 1) {
                $result = $matches[1];
            }
        } catch (FilesystemException) {
            // ignore filesystem extensions
        }
        return $result;
    }
}
