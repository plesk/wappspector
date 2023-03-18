<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class Typo3Matcher implements WappMatcherInterface
{
    /**
     * Version detection information for TYPO3 CMS 4.x and 6.x
     */
    protected const VERSIONS = [
        [
            'filename' => '/typo3/sysext/core/Classes/Core/SystemEnvironmentBuilder.php',
            'regexp' => '/define\\(\'TYPO3_version\', \'(.*?)\'\\)/'
        ],
        [
            'filename' => '/t3lib/config_default.php',
            'regexp' => '/TYPO_VERSION = \'(.*?)\'/'
        ],
    ];

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        foreach (self::VERSIONS as $version) {
            $versionFile = $version['filename'];

            if (!$fs->fileExists($versionFile)) {
                return [];
            }

            if (preg_match($version['regexp'], $fs->read($versionFile), $matches) && count($matches) > 1) {
                return [[
                    'matcher' => 'typo3 CMS',
                    'version' => $matches[1],
                    'path' => $path,
                ]];
            }
        }

        return [];
    }
}
