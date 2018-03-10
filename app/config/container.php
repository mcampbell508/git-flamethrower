<?php

use MCampbell508\Git\Flamethrower\Console\CLImate;
use Pimple\Container;

$container = new Container();

$container['application.CLImate'] = function(Container $container) {
    return new CLImate();
};

$container['application.command'] = function(Container $container) {
    return new \MCampbell508\Git\Flamethrower\Console\Command(
        $container['application.CLImate']
    );
};

$container['commands'] = function($container) {
    return [

    ];
};

$container['application'] = function($container) {
    $application = new \Symfony\Component\Console\Application(
        'Git Flamethrower Command Line Tool',
        '1.0.0'
    );
    $application->addCommands($container['commands']);

    return $application;
};

return $container;