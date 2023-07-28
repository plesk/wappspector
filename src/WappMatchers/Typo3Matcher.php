<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\Matchers;

class Typo3Matcher implements WappMatcherInterface
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

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        foreach (self::VERSIONS as $version) {
            $versionFile = rtrim($path, '/') . '/' . $version['filename'];

            if (!$fs->fileExists($versionFile)) {
                continue;
            }

            if ($version = $this->detectVersion($version['regexp'], $versionFile, $fs)) {
                return [
                    'matcher' => Matchers::TYPO3,
                    'version' => $version,
                    'path' => $path,
                ];
            }
        }

        return [];
    }

    public function detectVersion(string $regexPattern, string $versionFile, Filesystem $fs): ?string
    {
        preg_match($regexPattern, $fs->read($versionFile), $matches);
        return count($matches) > 1 ? $matches[1] : null;
    }
}
