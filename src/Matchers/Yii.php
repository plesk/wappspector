<?php

declare(strict_types=1);


namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Yii as MatchResult;

class Yii implements MatcherInterface
{
    private const VERSIONS = [
        [
            'file' => 'yii',
            'versionFile' => '/vendor/yiisoft/yii2/BaseYii.php',
            'versionRegexp' => '/public static function getVersion\(\)\s*\{\s*return \'([^\']+)\';\s*}/',
        ],
        [
            'file' => 'framework/yiic',
            'versionFile' => '/framework/YiiBase.php',
            'versionRegexp' => '/public static function getVersion\(\)\s*\{\s*return \'([^\']+)\';\s*}/',
        ],
    ];

    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $path = rtrim($path, '/');

        foreach (self::VERSIONS as $version) {
            if (!$fs->fileExists($path . '/' . $version['file'])) {
                continue;
            }
            return new MatchResult($path, $this->detectVersion($fs, $path, $version));
        }

        return new EmptyMatchResult();
    }

    private function detectVersion(Filesystem $fs, string $path, array $versionInfo): ?string
    {
        $version = null;

        $yii2VersionFile = $path . $versionInfo['versionFile'];
        if ($fs->fileExists($yii2VersionFile)) {
            preg_match($versionInfo['versionRegexp'], $fs->read($yii2VersionFile), $matches);

            if (isset($matches[1])) {
                $version = $matches[1];
            }
        }

        return $version;
    }
}
