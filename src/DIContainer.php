<?php

namespace Plesk\Wappspector;

use DI\Container;
use DI\ContainerBuilder;
use Exception;

class DIContainer
{
    /**
     * @throws Exception
     */
    public static function build(): Container
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(__DIR__ . '/container.php');

        return $containerBuilder->build();
    }
}
