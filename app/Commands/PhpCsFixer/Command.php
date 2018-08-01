<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Commands\PhpCsFixer;

use Shopworks\Git\Review\BaseCommand;

class Command extends BaseCommand
{
    protected $signature = 'php-cs-fixer';
    protected $toolName = 'PHP-CS-Fixer';
    protected $config = 'tools.php_cs_fixer';
    protected $configPaths = 'tools.php_cs_fixer.paths';
    protected $cliCommand = CLICommand::class;
}
