<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Plesk\Wappspector\Command\Inspect;
use Plesk\Wappspector\WappMatchers;
use Plesk\Wappspector\Wappspector;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use function DI\get;

return [
    WappMatchers\WappMatcherInterface::class => [
        get(WappMatchers\PhpMatcher::class),
        get(WappMatchers\ComposerMatcher::class),
        get(WappMatchers\WordpressMatcher::class),
    ],
    Filesystem::class => static function (ContainerInterface $container): Filesystem {
        $adapter = new LocalFilesystemAdapter($container->get('path'));

        return new Filesystem($adapter);
    },
    Wappspector::class => static function (ContainerInterface $container): Wappspector {
        return new Wappspector($container);
    },
    Inspect::class => static function (ContainerInterface $container): Inspect {
        return new Inspect($container->get(Wappspector::class));
    },
    Application::class => static function (ContainerInterface $container): Application {
        $application = new Application('Wappspector');
        $inspectCommand = $container->get(Inspect::class);

        $application->add($inspectCommand);
        $application->setDefaultCommand($inspectCommand->getName(), true);

        return $application;
    },
];
