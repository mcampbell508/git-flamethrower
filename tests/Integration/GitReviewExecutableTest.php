<?php

namespace Shopworks\Tests\Integration;

use Shopworks\Git\Review\Process\Process;
use Shopworks\Tests\TestCase;

class GitReviewExecutableTest extends TestCase
{
    /** @test */
    public function it_displays_the_command_options_correctly(): void
    {
        $process = new Process("./git-review");
        $process->run();

        $this->assertTrue($process->isSuccessful());

        $processOutput = \trim($process->getOutput());

        $this->assertContains(
            "  USAGE: git-review <command> [options] [arguments]",
            $processOutput
        );

        $this->assertContains(
            "  es-lint      Run ESLint on only the changed files on a Git topic branch. All files on "
            ."`master` will be checked.",
            $processOutput
        );
    }
}
