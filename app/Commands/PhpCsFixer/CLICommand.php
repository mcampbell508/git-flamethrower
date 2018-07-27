<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Commands\PhpCsFixer;

use Illuminate\Support\Arr;
use Shopworks\Git\Review\Commands\CliCommandContract;

class CLICommand implements CliCommandContract
{
    private $config;
    private $filePaths;

    public function __construct(array $config, array $filePaths)
    {
        $this->config = $config;
        $this->filePaths = $filePaths;
    }

    public function toString(): string
    {
        $binPath = Arr::get($this->config, 'bin_path', 'vendor/bin/php-cs-fixer');

        $additionalOptions = $this->addVerbosityLevel(
            $this->addDryRunOption(
                $this->addConfigPath(
                    $this->addFilePaths()
                )
            )
        );

        return "php {$binPath} fix" . $additionalOptions;
    }

    private function addFilePaths(string $cmd = ''): string
    {
        return "$cmd " . \implode(' ', $this->filePaths);
    }

    private function addConfigPath(string $cmd): string
    {
        $configPath = Arr::get($this->config, 'config_path', '');

        if (\mb_strlen($configPath) > 0) {
            $cmd .= " --config={$configPath}";
        }

        return $cmd;
    }

    private function addDryRunOption(string $cmd): string
    {
        $dryRun = (bool)Arr::get($this->config, 'dry_run_mode', true);

        return $cmd .= $dryRun ? ' --dry-run' : '';
    }

    private function addVerbosityLevel(string $cmd): string
    {
        $verbosity = (int)Arr::get($this->config, 'verbosity_level', 0);

        if ($verbosity === 0) {
            return $cmd;
        }

        switch ($verbosity) {
            case 1:
                return $cmd .= ' -v';
            case 2:
                return $cmd .= ' -vv';
            case 3:
                return $cmd .= ' -vvv';
            default:
                return $cmd;
        }
    }
}
