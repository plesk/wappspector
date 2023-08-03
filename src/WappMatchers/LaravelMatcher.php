<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Plesk\Wappspector\Matchers;

class LaravelMatcher implements WappMatcherInterface
{
    use UpLevelMatcherTrait;

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

        $json = [];
        try {
            $json = json_decode($fs->read($path . '/' . self::COMPOSER_JSON), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            // ignore composer.json errors
        }
        $laravelPackage = $json['require']['laravel/framework'] ?? null;

        return [
            'matcher' => Matchers::LARAVEL,
            'path' => $path,
            'version' => $laravelPackage !== null ? str_replace('^', '', $laravelPackage) : null,
        ];
    }
}
