<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Commands\ESLint;

use Shopworks\Git\Review\BaseCommand;

class Command extends BaseCommand
{
    protected $signature = 'es-lint';
    protected $toolName = 'ESLint';
    protected $config = 'tools.es_lint';
    protected $configPaths = 'tools.es_lint.paths';
    protected $cliCommand = CLICommand::class;
}
