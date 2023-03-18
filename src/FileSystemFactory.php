<?php

namespace Plesk\Wappspector;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class FileSystemFactory
{
    public function create(string $path): Filesystem
    {
        $adapter = new LocalFilesystemAdapter($path);

        return new Filesystem($adapter);
    }
}
