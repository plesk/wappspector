<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\Matchers;

class LaravelMatcher implements WappMatcherInterface
{
    use UpLevelMatcherTrait;

    private const VERSION_FILE = 'vendor/laravel/framework/src/Illuminate/Foundation/Application.php';
    private const COMPOSER_JSON = 'composer.json';
    private const ARTISAN = 'artisan';

    /**
     * @throws FilesystemException
     */
    protected function doMatch(Filesystem $fs, string $path): array
    {
        $path = rtrim($path, '/');
        if (!$fs->fileExists($path . '/' . self::ARTISAN)) {
            return [];
        }

        return [
            'matcher' => Matchers::LARAVEL,
            'path' => $path,
            'version' => $this->detectVersion($path, $fs),
        ];
    }

    private function detectVersion(string $path, Filesystem $fs): ?string
    {
        $result = null;
        $versionFile = $path . '/' . self::VERSION_FILE;
        if ($fs->fileExists($versionFile)) {
            preg_match("/VERSION\\s*=\\s*'([^']+)'/", $fs->read($versionFile), $matches);
            if (count($matches)) {
                $result = $matches[1];
            }
        } else {
            $composerJsonFile = $path . '/' . self::COMPOSER_JSON;
            if ($fs->fileExists($composerJsonFile)) {
                try {
                    $json = json_decode($fs->read($composerJsonFile), true, 512, JSON_THROW_ON_ERROR);
                    if ($laravelPackage = $json['require']['laravel/framework'] ?? null) {
                        $result = str_replace('^', '', $laravelPackage);
                    }
                } catch (\JsonException $e) {
                    // ignore composer.json errors
                }
            }
        }

        return $result;
    }
}
