<?php

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo 'You must set up the project dependencies using `composer install`';
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../vendor/autoload.php';

$container = require(__DIR__ . '/config/container.php');
