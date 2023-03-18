<?php

namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class LaravelMatcher implements WappMatcherInterface
{
    private const COMPOSER_JSON = 'composer.json';

    /**
     * @throws FilesystemException
     */
    public function match(Filesystem $fs, string $path): iterable
    {
        if (!$fs->fileExists(self::COMPOSER_JSON)) {
            return [];
        }

        $laravelPackage = $this->getLaravelPackage($fs);
        if ($laravelPackage) {
            return [
                'matcher' => 'laravel',
                'path' => $path,
                'version' => $laravelPackage,
            ];
        }

        return [];
    }

    private function getLaravelPackage(Filesystem $fs): ?string
    {
        $composerJson = json_decode($fs->read(self::COMPOSER_JSON), JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
        $laravelPackage = $composerJson['require']['laravel/framework'] ?? null;

        if ($laravelPackage !== null) {
            return str_replace('^', '', $laravelPackage);
        }

        return null;
    }
}
