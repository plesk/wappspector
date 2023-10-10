<?php

declare(strict_types=1);


namespace Plesk\Wappspector\Matchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\MatchResult\EmptyMatchResult;
use Plesk\Wappspector\MatchResult\MatchResultInterface;
use Plesk\Wappspector\MatchResult\Yii as MatchResult;

class Yii implements MatcherInterface
{
    public function match(Filesystem $fs, string $path): MatchResultInterface
    {
        $path = rtrim($path, '/');
        if (!$fs->fileExists($path . '/yii')) {
            return new EmptyMatchResult();
        }

        return new MatchResult($path, $this->detectVersion($fs, $path));
    }

    private function detectVersion(Filesystem $fs, string $path): ?string
    {
        $version = null;

        $yii2VersionFile = $path . '/vendor/yiisoft/yii2/BaseYii.php';
        if ($fs->fileExists($yii2VersionFile)) {
            // Use regular expression to match the getVersion method content
            preg_match(
                '/public static function getVersion\(\)\s*\{\s*return \'([^\']+)\';\s*}/',
                $fs->read($yii2VersionFile),
                $matches
            );

            // Check if the match is found and return the version
            if (isset($matches[1])) {
                $version = $matches[1];
            }
        }

        return $version;
    }
}
