<?php

namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Typo3 as MatchResult;

class Typo3 implements MatcherInterface
{
    /**
     * Version detection information for TYPO3 CMS 4.x and 6.x
     */
    protected const VERSIONS = [
        [
            'filename' => 'typo3/sysext/core/Classes/Information/Typo3Version.php',
            'regexp' => '/VERSION = \'(.*?)\'/',
        ],
        [
            'filename' => 'typo3/sysext/core/Classes/Core/SystemEnvironmentBuilder.php',
            'regexp' => '/define\\(\'TYPO3_version\', \'(.*?)\'\\)/',
        ],
        [
            'filename' => 't3lib/config_default.php',
            'regexp' => '/TYPO_VERSION = \'(.*?)\'/',
        ],
    ];

    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        foreach (self::VERSIONS as $version) {
            $versionFile = rtrim($path, '/') . '/' . $version['filename'];

            if (!$fs->fileExists($versionFile)) {
                continue;
            }

            if ($version = $this->detectVersion($version['regexp'], $versionFile, $fs)) {
                return new MatchResult($path, $version);
            }
        }

        return new EmptyMatchResult();
    }

    public function detectVersion(string $regexPattern, string $versionFile, Filesystem $fs): ?string
    {
        try {
            preg_match($regexPattern, $fs->read($versionFile), $matches);
            return count($matches) > 1 ? $matches[1] : null;
        } catch (FilesystemException) {
            // ignore file reading problem
            return null;
        }
    }
}
