<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Plesk\Wappspector\WappMatchers\PhpMatcher;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;
use function DI\get;

return [
    WappMatcherInterface::class => [
        get(PhpMatcher::class),
    ],
    Filesystem::class => static function (): Filesystem {
        $adapter = new LocalFilesystemAdapter(getcwd());

        return new Filesystem($adapter);
    },
];
