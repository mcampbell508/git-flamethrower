<?php

declare(strict_types=1);

namespace Shopworks\Git\Review;

use LaravelZero\Framework\Commands\Command;
use Shopworks\Git\Review\File\GitFilesFinder;
use Shopworks\Git\Review\Process\Process;
use Shopworks\Git\Review\Process\Processor;
use Shopworks\Git\Review\Repositories\ConfigRepository;
use Shopworks\Git\Review\VersionControl\GitBranch;

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
}
