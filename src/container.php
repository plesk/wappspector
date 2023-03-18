<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Plesk\Wappspector\WappMatchers;
use Psr\Container\ContainerInterface;
use function DI\get;

return [
    WappMatchers\WappMatcherInterface::class => [
        get(WappMatchers\PhpMatcher::class),
        get(WappMatchers\ComposerMatcher::class),
    ],
    Filesystem::class => static function (ContainerInterface $container): Filesystem {
        $adapter = new LocalFilesystemAdapter($container->get('path'));

        return new Filesystem($adapter);
    },
];
