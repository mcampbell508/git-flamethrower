<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\VersionControl;

use Illuminate\Support\Collection;
use OndraM\CiDetector\CiDetector;
use Shopworks\Git\Review\File\FileCollection;
use Shopworks\Git\Review\Process\Process;
use Shopworks\Git\Review\Process\Processor;

class GitBranch
{
    private $ciDetector;
    private $currentWorkingDirectory;
    private $gitBinary;
    private $processor;

    public function __construct(
        CiDetector $ciDetector,
        string $currentWorkingDirectory = __DIR__ . '/../../',
        string $gitBinary = '/usr/bin/git'
    ) {
        $this->ciDetector = $ciDetector;
        $this->currentWorkingDirectory = $currentWorkingDirectory;
        $this->gitBinary = $gitBinary;
        $this->processor = new Processor();
    }

    public function getName(): string
    {
        $process = Process::simple($this->gitBinary . ' symbolic-ref HEAD | sed -e "s/^refs\/heads\///"');

        return $this->ciDetector->isCiDetected() ?
            $this->ciDetector->detect()->getGitBranch() :
            $this->processor->process($process)->getOutput();
    }

    public function getChangedFiles(): Collection
    {
        $fileCollection = new FileCollection($this->currentWorkingDirectory);

        $branchName = $this->getName();

        $process = Process::simple(
            $this->gitBinary . " log --name-status --pretty=format: {$this->getParentHash()}..${branchName}" .
            " | grep -E '^[A-Z]\\b' | sort | uniq"
        );

        $committedFilesProcess = $this->processor->process($process);

        if (!$committedFilesProcess->isSuccessful()) {
            return new Collection();
        }

        $fileCollection->addFiles(\array_filter(\explode(\PHP_EOL, $committedFilesProcess->getOutput())));

        return $fileCollection->getFileCollection();
    }

    /**
     * A master branch using the merge commit strategy will return 2 parent hashes:
     *  - the ancestor (commit before the merge commit)
     *  - the actual parent we need in this case
     *
     * A master branch using the rebase strategy will usually have one parent hash.
     */
    public function getParentHash(): string
    {
        if ($this->isEmpty()) {
            return $this->getLatestCommitId();
        }

        $process = Process::simple(
            $this->gitBinary . " rev-list --boundary {$this->getName()}...master | grep '^-' | cut -c2-"
        );

        $hashes = $this->processor
            ->process($process)
            ->getOutput();

        $hashes = \explode(\PHP_EOL, $hashes);

        if (\count($hashes) === 1) {
            return $hashes[0];
        }

        return $hashes[1];
    }

    public function isDirty(): bool
    {
        $process = Process::simple($this->gitBinary . ' status --short');

        return !empty($this->processor->process($process)->getOutput());
    }

    public function isEmpty(): bool
    {
        $command = \trim($this->processor
            ->process(Process::simple(
                $this->gitBinary . " branch -a --contains {$this->getLatestCommitId()} | grep -E '(^|\\s)master$'"
            ))
            ->getOutput());

        return $command === 'master' || $command === 'remotes/origin/master';
    }

    public function getLatestCommitId(): string
    {
        return $this->processor
            ->process(Process::simple(
                $this->gitBinary . " rev-parse HEAD"
            ))
            ->getOutput();
    }
}
