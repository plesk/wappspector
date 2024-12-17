<?php

use DI\Container;
use Plesk\Wappspector\Command\Inspect;
use Plesk\Wappspector\FileSystemFactory;
use Plesk\Wappspector\Matchers;
use Plesk\Wappspector\Wappspector;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

return [
    'matchers' => [
        Matchers\Wordpress::class,
        Matchers\Joomla::class,
        Matchers\Drupal::class,
        Matchers\Prestashop::class,
        Matchers\Typo3::class,
        Matchers\Laravel::class,
        Matchers\Symfony::class,
        Matchers\CodeIgniter::class,
        Matchers\CakePHP::class,
        Matchers\Yii::class,
        Matchers\DotNet::class,
        Matchers\Ruby::class,
        Matchers\Python::class,
        Matchers\NodeJs::class,
        Matchers\Sitejet::class,
        Matchers\WebPresenceBuilder::class,
        Matchers\Sitepro::class,
        Matchers\Duda::class,
        Matchers\Siteplus::class,

        // Low priority wrappers. Should go last.
        Matchers\Composer::class,
        Matchers\Php::class,
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
