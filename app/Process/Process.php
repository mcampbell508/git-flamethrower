<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Process;

use Symfony\Component\Process\Process as SymfonyProcess;

class Process extends SymfonyProcess
{
    public function __construct(
        $commandline = '',
        ?string $cwd = null,
        ?array $env = null,
        $input = null,
        $timeout = 60
    ) {
        parent::__construct($commandline, $cwd, $env, $input, $timeout);
    }

    public static function simple(string $command): self
    {
        return new static($command);
    }

    public function getOutput()
    {
        return \trim(parent::getOutput());
    }
}
