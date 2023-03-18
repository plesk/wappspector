<?php

namespace Plesk\Wappspector;

use DI\Container;
use DI\ContainerBuilder;
use Exception;
use Plesk\Wappspector\WappMatchers\WappMatcherInterface;

final class Wappspector
{
    /**
     * @throws Exception
     */
    private static function buildContainer(): Container
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(__DIR__ . '/container.php');

        return $containerBuilder->build();
    }

    /**
     * @throws Exception
     */
    public static function run(string $path): iterable
    {
        $container = self::buildContainer();
        $container->set('path', $path);
        $matchers = $container->get(WappMatcherInterface::class);
        $result = [];

        /** @var WappMatcherInterface $matcher */
        foreach ($matchers as $matcher) {
            $result = [...$result, ...$matcher->match('/')];
        }

        return $result;
    }
}
