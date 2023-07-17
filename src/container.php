<?php

use DI\Container;
use Plesk\Wappspector\Command\Inspect;
use Plesk\Wappspector\FileSystemFactory;
use Plesk\Wappspector\WappMatchers;
use Plesk\Wappspector\Wappspector;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

return [
    'matchers' => [
        WappMatchers\JoomlaMatcher::class,
        WappMatchers\WordpressMatcher::class,
        WappMatchers\DrupalMatcher::class,
        WappMatchers\PrestashopMatcher::class,
        WappMatchers\Typo3Matcher::class,
        WappMatchers\LaravelMatcher::class,
        WappMatchers\DotNetMatcher::class,
        WappMatchers\RubyMatcher::class,
        WappMatchers\PythonMatcher::class,
        WappMatchers\NodeJsMatcher::class,

        // Low priority wrappers. Should go last.
        WappMatchers\ComposerMatcher::class,
        WappMatchers\PhpMatcher::class,
    ],
    Wappspector::class => static function (Container $container): Wappspector {
        $matchers = [];

        foreach ($container->get('matchers') as $matcher) {
            $matchers[] = $container->get($matcher);
        }

        return new Wappspector($container->get(FileSystemFactory::class), $matchers);
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
