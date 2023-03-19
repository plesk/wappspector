<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

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
        if (!$fs->fileExists(rtrim($path, '/') . '/' . self::ARTISAN)) {
            return [];
        }

        $composerJson = json_decode($fs->read(rtrim($path, '/') . '/' . self::COMPOSER_JSON), JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
        $laravelPackage = $composerJson['require']['laravel/framework'] ?? null;

        return [
            'matcher' => 'laravel',
            'path' => $path,
            'version' => $laravelPackage !== null ? str_replace('^', '', $laravelPackage) : null,
        ];
    }
}
