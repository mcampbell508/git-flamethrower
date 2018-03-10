#!/usr/bin/env php
<?php

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo 'You must set up the project dependencies using `composer install`';
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

use MCampbell508\Git\Flamethrower\Console\Application;

$name    = 'Git Flamethrower Command Line Tool';
$version = '1.0.0';

$console = new Application($name, $version);

$console->addCommands([

]);

$console->run();