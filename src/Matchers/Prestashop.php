<?php

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Prestashop as MatchResult;

class Prestashop implements MatcherInterface
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

            return new MatchResult($path, $this->getVersion($version, $fs, $versionFile));
        }

        return new EmptyMatchResult();
    }

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
