<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\Matchers;

class DrupalMatcher implements WappMatcherInterface
{
    /**
     * Drupal has changed the way how the version number is stored multiple times, so we need this comprehensive array
     */
    private const VERSIONS = [
        [
            'file' => '/modules/system/system.info',
            'regex' => "/version\\s*=\\s*\"(\\d\\.[^']+)\"[\\s\\S]*project\\s*=\\s*\"drupal\"/",
        ],
        [
            'file' => '/core/modules/system/system.info.yml',
            'regex' => "/version:\\s*'(\\d\\.[^']+)'[\\s\\S]*project:\\s*'drupal'/",
        ],
    ];

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        // Iterate through version patterns
        foreach (self::VERSIONS as $version) {
            $versionFile = rtrim($path, '/') . '/' . $version['file'];

            if (!$fs->fileExists($versionFile)) {
                continue;
            }

            preg_match($version['regex'], $fs->read($versionFile), $matches);

            if (!count($matches)) {
                continue;
            }

            return [
                'matcher' => Matchers::DRUPAL,
                'version' => $matches[1],
                'path' => $path,
            ];
        }

        return [];
    }
}
