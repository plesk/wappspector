<?php

namespace Plesk\Wappspector;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class FileSystemFactory
{
    public function __invoke(string $path): Filesystem
    {
        $adapter = new LocalFilesystemAdapter($path, null, LOCK_EX, LocalFilesystemAdapter::SKIP_LINKS);

        return new Filesystem($adapter);
    }
}
