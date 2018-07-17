<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\File;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Shopworks\Git\Review\VersionControl\GitBranch;
use Spatie\Regex\Regex;
use StaticReview\File\File;

class GitFilesFinder
{
    private $gitBranch;
    private $criteriaFormatter;
    private $filesystem;

    public function __construct(GitBranch $gitBranch, CriteriaFormatter $criteriaFormatter, Filesystem $filesystem)
    {
        $this->gitBranch = $gitBranch;
        $this->criteriaFormatter = $criteriaFormatter;
        $this->filesystem = $filesystem;
    }

    public function find(array $pathCriteria = [], array $extensionCriteria = []): Collection
    {
        $files = $this->gitBranch->getChangedFiles();
        $pathCriteria = $this->criteriaFormatter->formatMany($pathCriteria);

        return $this->findFilesByGivenCriteria($files, $pathCriteria, $extensionCriteria);
    }

    public function getBranchName(): string
    {
        return $this->gitBranch->getName();
    }

    private function findFilesByGivenCriteria(
        Collection $files,
        array $pathCriteria,
        array $extensionCriteria
    ): Collection {
        return $files->filter(function (File $file) use ($pathCriteria) {
            return collect($pathCriteria)->contains(function ($criteria) use ($file) {
                return Regex::match($criteria, $file->getRelativePath())->hasMatch();
            });
        })->reject(function (File $file) use ($extensionCriteria) {
            $fileNotExists = !$this->filesystem->exists($file->getRelativePath());
            $invalidExtension = !empty($extensionCriteria)
                && !\in_array($file->getExtension(), $extensionCriteria, true);

            return $fileNotExists || $invalidExtension;
        })->sort()->values();
    }
}
