<?php

declare(strict_types=1);

namespace Shopworks\Git\Review;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
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
    }

    protected function configNotFound(): bool
    {
        $isEmpty = $this->configRepository->isEmpty();

        if ($isEmpty) {
            $this->getOutput()->error('No `git-review.yml.dist` or `git-review.yml` config file, found!');
        }

        return $isEmpty;
    }

    protected function runCommand(string $command, bool $realTimeOutput = false): Process
    {
        $process = $this->process->simple($command);

        return $this->processor->process($process, $realTimeOutput);
    }

    protected function resolveFilePaths(array $paths, string $config): array
    {
        $branchName = $this->gitFilesFinder->getBranchName();

        if ($branchName === 'master') {
            return $this->configRepository->get($config);
        }

        /** @var Collection $gitFiles */
        $gitFiles = $this->gitFilesFinder->find($paths);
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

    private function getFilesAsString(Collection $files): Collection
    {
        return $files->map(function (File $file) {
            return $file->getRelativePath();
        })->unique();
    }
}
