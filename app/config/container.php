<?php

use MCampbell508\Git\Flamethrower\Console\CLImate;
use MCampbell508\Git\Flamethrower\Console\Commands\Menu;
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

$container['command.menu'] = function(Container $container) {
    return new Menu($container['application.CLImate']);
};

$container['commands'] = function($container) {
    return [
        $container['command.menu'],
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