<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Yml;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class YmlConfiguration
{
    private $currentWorkingDirectory;

    public function __construct(string $currentWorkingDirectory)
    {
        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    public function getConfigPath(): ?string
    {
        $finder = new Finder();
        $iterator = $finder
            ->files()
            ->name('git-review.yml.dist')
            ->name('git-review.yml')
            ->depth(0)
            ->in($this->currentWorkingDirectory);

        return Collection::make($iterator)
            ->mapWithKeys(function (SplFileInfo $fileInfo) {
                return [$fileInfo->getFilename() => $fileInfo->getRealPath()];
            })
            ->sort()
            ->first(function ($item, $key) {
                return $key === 'git-review.yml' || $key === 'git-review.yml.dist';
            });
    }
}
