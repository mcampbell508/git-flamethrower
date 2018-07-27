<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Commands\ESLint;

use Shopworks\Git\Review\BaseCommand;

class Command extends BaseCommand
{
    protected $signature = 'es-lint';
    protected $description = 'Run ESLint on only the changed files on a Git topic branch. All files on `master` '
        . 'will be checked.';

    public function handle(): void
    {
        if ($this->configNotFound()) {
            return;
        }

        $esLintConfig = $this->configRepository->get('tools.es_lint');

        if (!isset($esLintConfig['paths'])) {
            $this->error("No paths have been specified in the config file!");

            return;
        }

        $branchName = $this->gitFilesFinder->getBranchName();

        if ($branchName !== 'master' && $this->gitBranch->isEmpty()) {
            $this->info("This branch is empty, exiting!");

            return;
        }

        $this->getOutput()->title('Filtering changed files on branch using the following paths:');
        $this->getOutput()->listing($esLintConfig['paths']);

        $filePaths = $this->resolveFilePaths($esLintConfig['paths'], 'tools.es_lint.paths');

        if (empty($filePaths)) {
            $this->getOutput()->writeln('No files to scan matching provided filters, nothing to do!');

            return;
        }

        $command = new CLICommand($esLintConfig, $filePaths);
        $commandString = $command->toString();

        $this->getOutput()->writeln("\n<options=bold,underscore>Running command:</>\n");
        $this->getOutput()->writeln("<info>{$commandString}</info>\n");

        $process = $this->runCommand($commandString, true);

        if ($process->getExitCode() !== 0) {
            $this->getOutput()->writeln("\n<error>ESLint failed.</error>");

             return;
        }

        $this->getOutput()->newLine();
        $this->getOutput()->success('ESLint checks passed, good job!!!!');
    }
}
