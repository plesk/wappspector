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
        $result = $this->getLaravelVersion($fs, $path);
        if (!$result) {
            return $this->getLaravelVersion($fs, rtrim($path) . '/../');
        }

        return $result;
    }

    /**
     * @throws FilesystemException
     */
    private function getLaravelVersion(Filesystem $fs, string $path): ?array
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
