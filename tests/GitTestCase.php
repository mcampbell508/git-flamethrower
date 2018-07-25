<?php

declare(strict_types=1);

namespace Shopworks\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class GitTestCase extends TestCase
{
    protected $directory;
    protected $testFileName;

    public function setUp(): void
    {
        $this->directory = \sys_get_temp_dir() . '/git-review-tests/';

        if (!\is_dir($this->directory)) {
            \mkdir($this->directory, 0755, true);
        } else {
            $this->runProcess('rm -rf ' . \sys_get_temp_dir() . '/git-review-tests/' . \DIRECTORY_SEPARATOR . '*');
        }

        $this->directory = \realpath($this->directory);
        $this->testFileName = 'test.txt';

        \chdir($this->directory);
        $this->runProcess("/usr/bin/git init");
    }

    public function tearDown(): void
    {
        $this->runProcess('rm -rf ' . \sys_get_temp_dir() . '/git-review-tests/');

        \Mockery::close();

        parent::tearDown();
    }

    protected function runProcess(string $command): Process
    {
        $process = new Process($command, $this->directory);
        $process->run();

        return $process;
    }
}
