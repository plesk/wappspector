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
            $versionFile = rtrim($path, '/') . '/' . $version['filename'];

            if (!$fs->fileExists($versionFile)) {
                continue;
            }

            $result = [
                'matcher' => 'prestashop',
                'path' => $path,
            ];

            if (preg_match($version['regexp'], $fs->read($versionFile), $matches)) {
                if (count($matches) > 1) {
                    $result['version'] = $matches[1];
                }
            }

            return $result;
        }

        return [];
    }
}
