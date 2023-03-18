#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Plesk\Wappspector\Command;

$application = new Application('Wappspector');
$command = new Command\Inspect();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();
