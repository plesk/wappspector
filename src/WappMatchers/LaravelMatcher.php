<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class LaravelMatcher implements WappMatcherInterface
{
    private const COMPOSER_JSON = 'composer.json';
    private const ARTISAN = 'artisan';

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        if (!$fs->fileExists(rtrim($path, '/') . '/' . self::ARTISAN)) {
            return [];
        }

        $laravelPackage = $this->getLaravelVersion($fs, $path);
        if ($laravelPackage) {
            return [
                'matcher' => 'laravel',
                'path' => $path,
                'version' => $laravelPackage,
            ];
        }

        return [];
    }

    private function getLaravelVersion(Filesystem $fs, string $path): ?string
    {
        $composerJson = json_decode($fs->read(rtrim($path, '/') . '/' . self::COMPOSER_JSON), JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
        $laravelPackage = $composerJson['require']['laravel/framework'] ?? null;

        if ($laravelPackage !== null) {
            return str_replace('^', '', $laravelPackage);
        }

        return null;
    }
}
