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
            'file' => 'modules/system/system.info',
            'regex' => "/version\\s*=\\s*\"(\\d\\.[^']+)\"[\\s\\S]*project\\s*=\\s*\"drupal\"/",
        ],
        [
            'file' => 'core/modules/system/system.info.yml',
            'regex' => "/version:\\s*'(\\d+\\.[^']+)'[\\s\\S]*project:\\s*'drupal'/",
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

            $version = $this->detectVersion($version['regex'], $versionFile, $fs);
            return [
                'matcher' => Matchers::DRUPAL,
                'version' => $version,
                'path' => $path,
            ];
        }

        return [];
    }

    private function detectVersion(string $regexPattern, string $versionFile, Filesystem $fs): ?string
    {
        preg_match($regexPattern, $fs->read($versionFile), $matches);

        return count($matches) ? $matches[1] : null;
    }
}
