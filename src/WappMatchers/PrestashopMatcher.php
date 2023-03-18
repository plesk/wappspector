<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class PrestashopMatcher implements WappMatcherInterface
{
    protected const VERSIONS = [
        [
            'filename' => '/config/settings.inc.php',
            'regexp' => '/define\\(\'_PS_VERSION_\', \'(.+)\'\\)/'
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
                continue;
            }

            if (preg_match($version['regexp'], $fs->read($versionFile), $matches)) {
                if (count($matches) > 1) {
                    return [[
                        'matcher' => 'prestashop',
                        'version' => $matches[1],
                        'path' => $path,
                    ]];
                }
            }
        }

        return [];
    }
}