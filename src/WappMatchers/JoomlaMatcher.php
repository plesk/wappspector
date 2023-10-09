<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\JoomlaMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;

class JoomlaMatcher implements WappMatcherInterface
{
    private const CONFIG_FILE = 'configuration.php';

    /**
     * Joomla has changed the way how the version number is stored multiple times, so we need this comprehensive array
     */
    private const VERSION = [
        "files" => [
            "/includes/version.php",
            "/libraries/joomla/version.php",
            "/libraries/cms/version/version.php",
            "/libraries/src/Version.php",
        ],
        "regex_release" => "/\\\$?RELEASE\s*=\s*'([\d.]+)';/",
        "regex_devlevel" => "/\\\$?DEV_LEVEL\s*=\s*'([^']+)';/",
        "regex_major" => "/\\\$?MAJOR_VERSION\s*=\s*([\d.]+);/",
        "regex_minor" => "/\\\$?MINOR_VERSION\s*=\s*([\d.]+);/",
        "regex_patch" => "/\\\$?PATCH_VERSION\s*=\s*([\d.]+);/",
    ];

    /**
     * @throws FilesystemException
     */
    private function isJoomla(Filesystem $fs, string $path): bool
    {
        $configFile = rtrim($path, '/') . '/' . self::CONFIG_FILE;

        if (!$fs->fileExists($configFile)) {
            return false;
        }

        $configContents = $fs->read($configFile);

        if (
            stripos($configContents, 'JConfig') === false
            && stripos($configContents, 'mosConfig') === false
        ) {
            return false;
        }

        // False positive "Akeeba Backup Installer"
        if (stripos($configContents, 'class ABIConfiguration') !== false) {
            return false;
        }

        // False positive mock file in unit test folder
        if (stripos($configContents, 'Joomla.UnitTest') !== false) {
            return false;
        }

        // False positive mock file in unit test folder
        return stripos($configContents, "Joomla\Framework\Test") === false;
    }

    /**
     * @throws FilesystemException
     */
    private function detectVersion(Filesystem $fs, string $path): ?string
    {
        // Iterate through version files
        foreach (self::VERSION['files'] as $file) {
            $versionFile = rtrim($path, '/') . '/' . $file;

            if (!$fs->fileExists($versionFile)) {
                continue;
            }

            $fileContents = $fs->read($versionFile);

            preg_match(self::VERSION['regex_major'], $fileContents, $major);
            preg_match(self::VERSION['regex_minor'], $fileContents, $minor);
            preg_match(self::VERSION['regex_patch'], $fileContents, $patch);

            if (count($major) && count($minor) && count($patch)) {
                return $major[1] . '.' . $minor[1] . '.' . $patch[1];
            }

            if (count($major) && count($minor)) {
                return $major[1] . '.' . $minor[1] . 'x';
            }

            if (count($major)) {
                return $major[1] . '.x.x';
            }

            // Legacy handling for all version < 3.8.0
            preg_match(self::VERSION['regex_release'], $fileContents, $release);
            preg_match(self::VERSION['regex_devlevel'], $fileContents, $devlevel);

            if (count($release) && count($devlevel)) {
                return $release[1] . '.' . $devlevel[1];
            }

            if (count($release)) {
                return $release[1] . '.x';
            }
        }

        return null;
    }

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        if (!$this->isJoomla($fs, $path)) {
            return new EmptyMatchResult();
        }

        return new JoomlaMatchResult($path, $this->detectVersion($fs, $path));
    }
}
