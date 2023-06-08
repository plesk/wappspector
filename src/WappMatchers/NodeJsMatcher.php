<?php

declare(strict_types=1);


namespace Plesk\Wappspector\WappMatchers;

use League\Flysystem\Filesystem;

class NodeJsMatcher implements WappMatcherInterface
{
    public function match(Filesystem $fs, string $path): iterable
    {
        $packageFile = rtrim($path, '/') . '/package.json';

        if (!$fs->fileExists($packageFile)) {
            return [];
        }
        $json = json_decode($fs->read($packageFile), false, 512, JSON_THROW_ON_ERROR);

        return [
            'matcher' => 'nodejs',
            'path' => $path,
            'application' => $json->name ?? null,
            'version' => $json->version ?? null,
        ];
    }
}
