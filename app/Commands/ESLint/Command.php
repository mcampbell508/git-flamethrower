<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Commands\ESLint;

use Shopworks\Git\Review\BaseCommand;

class Command extends BaseCommand
{
    protected $signature = 'es-lint';
    protected $description = 'Run ESLint on only the changed files on a Git topic branch. All files on `master` '
        . 'will be checked.';
    protected $successMessage = 'ESLint checks passed, good job!!!!';
    protected $errorMessage = 'ESLint failed.';
    protected $config = 'tools.es_lint';
    protected $configPaths = 'tools.es_lint.paths';
    protected $cliCommand = CLICommand::class;
}
