<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class JoomlaMatcher implements WappMatcherInterface
{
    /**
     * Joomla has changed the way how the version number is stored multiple times, so we need this comprehensive array
     * @var array
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

    public function __construct(private readonly Filesystem $fs)
    {
    }

    /**
     * @throws FilesystemException
     */
    public function isJoomla(string $currPath): bool
    {
        if (basename($currPath) !== 'configuration.php') {
            return false;
        }

        $configContents = $this->fs->read($currPath);

        if (stripos($configContents, 'JConfig') === false
            && stripos($configContents, 'mosConfig') === false) {
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
        if (stripos($configContents, "Joomla\Framework\Test") !== false) {
            return false;
        }

        return true;
    }

    /**
     * @throws FilesystemException
     */
    public function detectVersion(string $path): ?string
    {
        // Iterate through version files
        foreach (self::VERSION['files'] as $file) {
            $versionFile = basename($path) . $file;

            if (!$this->fs->fileExists($versionFile)) {
                continue;
            }

            $fileContents = $this->fs->read($versionFile);

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
    public function match(string $path): iterable
    {
        $result = [];
        $list = $this->fs->listContents($path);

        foreach ($list as $item) {
            $currPath = $item->path();

            if ($this->isJoomla($currPath)) {
                $result[] = [
                    'matcher' => 'joomla',
                    'version' => $this->detectVersion($path),
                    'path' => $path,
                ];
                break;
            }
        }

        return $result;
    }
}
