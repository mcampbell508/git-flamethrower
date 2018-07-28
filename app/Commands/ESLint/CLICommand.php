<?php

namespace Shopworks\Git\Review\Commands\ESLint;

use Illuminate\Support\Arr;

class CLICommand
{
    private $esLintConfig;
    private $filePaths;

    public function __construct(array $esLintConfig, array $filePaths)
    {
        $this->esLintConfig = $esLintConfig;
        $this->filePaths = $filePaths;
    }

    public function toString(): string
    {
        $esLintBin = Arr::get($this->esLintConfig, 'bin_path', 'node_modules/.bin/eslint');

        return $this->addConfigPath($this->addFilePaths($this->addExtensions($esLintBin)));
    }

    private function addExtensions(string $cmd): string
    {
        $extensions = \implode(' --ext ', Arr::get($this->esLintConfig, 'extensions', []));

        if (\mb_strlen($extensions) > 0) {
            $cmd .= ' --ext ' . $extensions;
        }

        return $cmd;
    }

    private function addFilePaths(string $cmd): string
    {
        return "$cmd " . \implode(' ', $this->filePaths);
    }

    private function addConfigPath(string $cmd): string
    {
        $configPath = Arr::get($this->esLintConfig, 'config_path', '');

        if (\mb_strlen($configPath) > 0) {
            $cmd .= " -c {$configPath}";
        }

        return $cmd;
    }
}
