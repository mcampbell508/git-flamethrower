<?php

declare(strict_types=1);

namespace Shopworks\Git\Review;

use Assert\Assertion;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Shopworks\Git\Review\Commands\CliCommandContract;
use Shopworks\Git\Review\File\GitFilesFinder;
use Shopworks\Git\Review\Process\Process;
use Shopworks\Git\Review\Process\Processor;
use Shopworks\Git\Review\Repositories\ConfigRepository;
use Shopworks\Git\Review\VersionControl\GitBranch;
use StaticReview\File\File;

class BaseCommand extends Command
{
    protected $processor;
    protected $gitFilesFinder;
    protected $configRepository;
    protected $gitBranch;
    protected $process;
    protected $commandString = '';
    protected $toolName = '';
    protected $realTimeOutput = true;
    protected $ymlConfig;
    protected $config;
    protected $configPaths;
    protected $cliCommand;

    public function __construct(
        Process $process,
        Processor $processor,
        GitFilesFinder $gitFilesFinder,
        ConfigRepository $configRepository,
        GitBranch $gitBranch
    ) {
        parent::__construct();

        $this->process = $process;
        $this->processor = $processor;
        $this->gitFilesFinder = $gitFilesFinder;
        $this->configRepository = $configRepository;
        $this->gitBranch = $gitBranch;

        $this->setDescription("Run {$this->toolName} on only the changed files on a Git topic branch. All files on "
            . "`master` will be checked.");
    }

    public function handle(): void
    {
        if ($this->configNotFound()) {
            return;
        }

        $this->ymlConfig = $this->configRepository->get($this->config, []);

        if (!isset($this->ymlConfig['paths'])) {
            $this->error("No paths have been specified in the config file!");

            return;
        }

        $branchName = $this->gitFilesFinder->getBranchName();

        if ($branchName !== 'master' && $this->gitBranch->isEmpty()) {
            $this->info("This branch is empty, exiting!");

            return;
        }

        $this->getOutput()->title('Filtering changed files on branch using the following paths:');
        $this->getOutput()->listing($this->ymlConfig['paths']);

        $paths = $this->resolveFilePaths($this->ymlConfig['paths'] ?? [], $this->configPaths);

        if (empty($paths)) {
            $this->getOutput()->writeln('No files to scan matching provided filters, nothing to do!');

            return;
        }

        $this->setCommandString($paths);

        $this->getOutput()->writeln("\n<options=bold,underscore>Running command:</>\n");
        $this->getOutput()->writeln("<info>{$this->commandString}</info>\n");

        $process = $this->runCommand();

        if ($process->getExitCode() !== 0) {
            $this->getOutput()->writeln("\n<error>{$this->getErrorMessage()}</error>");

            return;
        }

        $this->getOutput()->newLine();
        $this->getOutput()->success($this->getSuccessMessage());
    }

    protected function configNotFound(): bool
    {
        $isEmpty = $this->configRepository->isEmpty();

        if ($isEmpty) {
            $this->getOutput()->error('No `git-review.yml.dist` or `git-review.yml` config file, found!');
        }

        return $isEmpty;
    }

    protected function runCommand(): Process
    {
        $process = $this->process->simple($this->commandString);

        return $this->processor->process($process, $this->realTimeOutput);
    }

    protected function resolveFilePaths(array $paths, string $config): array
    {
        $branchName = $this->gitFilesFinder->getBranchName();

        if ($branchName === 'master') {
            return $this->configRepository->get($config, []);
        }

        /** @var Collection $gitFiles */
        $gitFiles = $this->gitFilesFinder->find($paths);
        $filePaths = $this->getFilesAsString($gitFiles);

        if ($filePaths->isEmpty()) {
            return [];
        }

        $this->getOutput()->writeln("<options=bold,underscore>Modified files on branch \"${branchName}\"</>\n");

        $this->getOutput()->writeln($gitFiles->map(function (File $file) {
            return $file->getName() . ' - ' . $file->getFormattedStatus();
        })->toArray());

        return $filePaths->toArray();
    }

    protected function setCommandString(array $paths): void
    {
        /** @var CliCommandContract $command */
        $command = new $this->cliCommand(
            $this->ymlConfig,
            $paths
        );

        Assertion::isInstanceOf($command, CliCommandContract::class);

        $this->commandString = $command->toString();
    }

    protected function getErrorMessage(): string
    {
        return "{$this->toolName} checks failed!";
    }

    protected function getSuccessMessage(): string
    {
        return "{$this->toolName} checks passed, good job!!!!";
    }

    private function getFilesAsString(Collection $files): Collection
    {
        return $files->map(function (File $file) {
            return $file->getRelativePath();
        })->unique();
    }
}
