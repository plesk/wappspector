<?php

use DI\Container;
use Plesk\Wappspector\Command\Inspect;
use Plesk\Wappspector\FileSystemFactory;
use Plesk\Wappspector\WappMatchers;
use Plesk\Wappspector\Wappspector;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

return [
    Wappspector::class => static function (Container $container): Wappspector {
        return new Wappspector($container->get(FileSystemFactory::class), [
            $container->get(WappMatchers\PhpMatcher::class),
            $container->get(WappMatchers\JoomlaMatcher::class),
            $container->get(WappMatchers\ComposerMatcher::class),
            $container->get(WappMatchers\WordpressMatcher::class),
            $container->get(WappMatchers\DrupalMatcher::class),
        ]);
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
