<?php

declare(strict_types=1);


namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\Matchers;

class YiiMatcher implements WappMatcherInterface
{

    public function match(Filesystem $fs, string $path): iterable
    {
        $path = rtrim($path, '/');
        if (!$fs->fileExists($path . '/yii')) {
            return [];
        }

        $version = $this->detectVersion($fs, $path);
        return [
            'matcher' => Matchers::YII,
            'path' => $path,
            'version' => $version,
        ];
    }

    private function detectVersion(Filesystem $fs, string $path)
    {
        $version = null;

        $yii2VersionFile = $path . '/vendor/yiisoft/yii2/BaseYii.php';
        if ($fs->fileExists($yii2VersionFile)) {
            // Use regular expression to match the getVersion method content
            preg_match('/public static function getVersion\(\)\s*\{\s*return \'([^\']+)\'\;\s*\}/', $fs->read($yii2VersionFile), $matches);

            // Check if the match is found and return the version
            if (isset($matches[1])) {
                $version = $matches[1];
            }
        }

        return $version;
    }
}
