<?php

declare(strict_types=1);


namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;
use Plesk\Wappspector\Matchers;

class NodeJsMatcher implements WappMatcherInterface
{
    public function match(Filesystem $fs, string $path): iterable
    {
        $packageFile = rtrim($path, '/') . '/package.json';

        if (!$fs->fileExists($packageFile)) {
            return [];
        }

        $json = [];
        try {
            $json = json_decode($fs->read($packageFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            // ignore package.json errors
        }

        return [
            'matcher' => Matchers::NODEJS,
            'path' => $path,
            'application' => $json['name'] ?? null,
            'version' => $json['version'] ?? null,
        ];
    }
}
