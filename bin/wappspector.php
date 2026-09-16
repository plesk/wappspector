#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Plesk\Wappspector\DIContainer;
use Symfony\Component\Console\Application;

// Report anything that goes wrong building or running the console application.
// PHP's display_errors is off on a default installation, so without this an
// uncaught error exits non-zero having printed nothing at all -- which says
// nothing about a missing dependency, an unreadable path, or a bad argument.
try {
    $diContainer = DIContainer::build();

    $app = $diContainer->get(Application::class);

    $app->run();
} catch (Throwable $e) {
    fwrite(
        STDERR,
        sprintf(
            "wappspector: %s: %s in %s on line %d\n",
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        )
    );

    exit(1);
}
