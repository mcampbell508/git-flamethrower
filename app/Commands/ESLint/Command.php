<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Commands\ESLint;

use Illuminate\Support\Collection;
use Shopworks\Git\Review\BaseCommand;
use StaticReview\File\File;

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

        $filePaths = $this->resolveFilePaths($esLintConfig);

        $command = new CLICommand($esLintConfig, $branchName, $filePaths);
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

    protected function getFilesAsString(Collection $files): Collection
    {
        return $files->map(function (File $file) {
            return $file->getRelativePath();
        })->unique();
    }

    private function resolveFilePaths(array $esLintConfig): array
    {
        $branchName = $this->gitFilesFinder->getBranchName();

        if ($branchName === 'master') {
            return $this->configRepository->get('tools.es_lint.paths');
        }

        /** @var Collection $gitFiles */
        $gitFiles = $this->gitFilesFinder->find($esLintConfig['paths']);
        $filePaths = $this->getFilesAsString($gitFiles);

        if ($filePaths->isEmpty()) {
            $this->getOutput()->writeln('No files to scan matching provided filters!');

            return [];
        }

        $this->getOutput()->writeln("<options=bold,underscore>Modified files on branch \"${branchName}\"</>\n");

        $this->getOutput()->writeln($gitFiles->map(function (File $file) {
            return $file->getName() . ' - ' . $file->getFormattedStatus();
        })->toArray());

        return $filePaths->toArray();
    }
}
