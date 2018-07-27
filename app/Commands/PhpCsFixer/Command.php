<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Commands\PhpCsFixer;

use Shopworks\Git\Review\BaseCommand;

class Command extends BaseCommand
{
    protected $signature = 'php-cs-fixer';
    protected $description = 'Run PHP-CS-Fixer on only the changed files on a Git topic branch. All files'
        . 'will be checked in specified paths, if current branch is `master`.';
    protected $errorMessage = 'PHP-CS-Fixer failed.';
    protected $successMessage = 'PHP-CS-Fixer checks passed, good job!!!!';
    protected $config = 'tools.php_cs_fixer';
    protected $configPaths = 'tools.php_cs_fixer.paths';
    protected $cliCommand = CLICommand::class;
}
