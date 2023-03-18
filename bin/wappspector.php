#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Plesk\Wappspector\DIContainer;
use Symfony\Component\Console\Application;

$diContainer = DIContainer::build();

$app = $diContainer->get(Application::class);

$app->run();
