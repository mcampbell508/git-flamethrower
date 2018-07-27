<?php

namespace Shopworks\Tests\Unit\Commands\PhpCsFixer;

use Illuminate\Container\Container;
use Mockery;
use Mockery\MockInterface;
use Shopworks\Git\Review\Commands\PhpCsFixer\Command;
use Shopworks\Git\Review\File\GitFilesFinder;
use Shopworks\Git\Review\Process\Process as GitReviewProcess;
use Shopworks\Git\Review\Process\Processor;
use Shopworks\Git\Review\Repositories\ConfigRepository;
use Shopworks\Git\Review\VersionControl\GitBranch;
use Shopworks\Tests\UnitTestCase;
use StaticReview\File\File;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTest extends UnitTestCase
{
    /** @var CommandTester $commandTester */
    private $commandTester;
    /** @var Command $command */
    private $command;
    /** @var MockInterface|GitReviewProcess $process */
    private $process;
    /** @var MockInterface|Processor $processor */
    private $processor;
    /** @var MockInterface|ConfigRepository $configRepository */
    private $configRepository;
    /** @var MockInterface|GitFilesFinder $gitFilesFinder */
    private $gitFilesFinder;
    /** @var MockInterface|GitBranch $git */
    private $git;

    protected function setUp(): void
    {
        parent::setUp();

        $this->git = Mockery::mock(GitBranch::class);
        $this->process = Mockery::mock(GitReviewProcess::class);
        $this->processor = Mockery::mock(Processor::class);
        $this->gitFilesFinder = Mockery::mock(GitFilesFinder::class);
        $this->configRepository = Mockery::mock(ConfigRepository::class);

        $this->command = new Command(
            $this->process,
            $this->processor,
            $this->gitFilesFinder,
            $this->configRepository,
            $this->git
        );
        $this->commandTester = $this->getCommandTester();
    }

    /** @test */
    public function it_returns_early_when_no_config_is_found(): void
    {
        $this->configRepository->shouldReceive('isEmpty')->once()->andReturn(true);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains("[ERROR] No `git-review.yml.dist` or `git-review.yml` config file, found!", $output);
    }

    /** @test */
    public function it_handles_when_no_paths_are_specified_in_the_config(): void
    {
        $this->configRepository->shouldReceive('isEmpty')->once()->andReturn(false);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer", [])->andReturn([]);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains("No paths have been specified in the config file!", $output);
        $this->assertNotContains("Filtering changed files on branch using the following paths:", $output);
    }

    /** @test */
    public function it_handles_when_a_branch_that_is_not_master_is_empty(): void
    {
        $this->configRepository->shouldReceive('isEmpty')->once()->andReturn(false);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer", [])
            ->andReturn(['paths' => ['/test']]);

        $this->gitFilesFinder->shouldReceive('getBranchName')->once()->andReturn("potatoes");
        $this->git->shouldReceive('isEmpty')->once()->andReturn(true);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains("This branch is empty, exiting!", $output);
        $this->assertNotContains("Filtering changed files on branch using the following paths:", $output);
    }

    /** @test */
    public function it_displays_an_error_message_in_the_console_when_the_php_cs_fixer_command_has_failed(): void
    {
        $this->configRepository->shouldReceive('isEmpty')->once()->andReturn(false);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer", [])->andReturn([
            'paths' => [
                'app/',
                'fruits/*/tests',
            ],
            'verbosity_level' => 1,
        ]);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer.paths", [])->andReturn([
            'app/',
            'fruits/*/tests',
        ]);

        $this->gitFilesFinder->shouldReceive('getBranchName')->andReturn("master");

        $this->process->shouldReceive('simple')
            ->with('php vendor/bin/php-cs-fixer fix app/ fruits/*/tests --dry-run -v')
            ->andReturn($process = Mockery::mock(GitReviewProcess::class));

        $this->processor->shouldReceive('process')->with($process, true)->andReturn($process);

        $process->shouldReceive('getExitCode')->once()->andReturn(1);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals(\file_get_contents(__DIR__ . '/../../../fixtures/PhpCsFixer/php-cs-fixer-failing.txt'), $output);
    }

    /** @test */
    public function it_runs_the_php_cs_fixer_command_on_all_specified_paths_and_extensions_when_the_current_checked_out_branch_is_master(): void
    {
        $this->configRepository->shouldReceive('isEmpty')->once()->andReturn(false);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer", [])->andReturn([
            'paths' => [
                'app/',
                'fruits/*/tests',
            ],
            'verbosity_level' => 1,
        ]);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer.paths", [])->andReturn([
            'app/',
            'fruits/*/tests',
        ]);

        $this->gitFilesFinder->shouldReceive('getBranchName')->andReturn("master");

        $this->process->shouldReceive('simple')
            ->with("php vendor/bin/php-cs-fixer fix app/ fruits/*/tests --dry-run -v")
            ->andReturn($process = Mockery::mock(GitReviewProcess::class));

        $this->processor->shouldReceive('process')->with($process, true)->andReturn($process);

        $process->shouldReceive('getExitCode')->once()->andReturn(0);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals(\file_get_contents(__DIR__ . '/../../../fixtures/PhpCsFixer/master-passing.txt'), $output);
    }

    /** @test */
    public function it_runs_the_php_cs_fixer_command_on_only_the_changed_files_filtered_by_extensions_when_the_current_checked_out_branch_is_not_master(): void
    {
        $this->configRepository->shouldReceive('isEmpty')->once()->andReturn(false);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer", [])->andReturn([
            'paths' => [
                'app/',
                'fruits/*/tests',
            ],
            'verbosity_level' => 1,
        ]);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer.paths", [])->andReturn([
            'app/',
            'fruits/*/tests',
        ]);

        $this->gitFilesFinder->shouldReceive('getBranchName')->andReturn("pineapples");
        $this->git->shouldReceive('isEmpty')->once()->andReturn(false);

        $this->gitFilesFinder->shouldReceive('find')->once()->andReturn(collect([
            new File('A', 'app/example1.php', '/tmp/repo-base'),
            new File('M', 'fruits/pineapples/tests/example2.php', '/tmp/repo-base'),
            new File('A', 'fruits/oranges/tests/example3.php', '/tmp/repo-base'),
        ]));

        $this->process->shouldReceive('simple')
            ->with(
                "php vendor/bin/php-cs-fixer fix app/example1.php fruits/pineapples/tests/example2.php" .
                " fruits/oranges/tests/example3.php --dry-run -v"
            )
            ->andReturn($process = Mockery::mock(GitReviewProcess::class));

        $this->processor->shouldReceive('process')->with($process, true)->andReturn($process);

        $process->shouldReceive('getExitCode')->once()->andReturn(0);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals(\file_get_contents(__DIR__ . '/../../../fixtures/PhpCsFixer/non-master-passing.txt'), $output);
    }

    /** @test */
    public function it_does_not_execute_the_php_cs_fixer_bin_command_when_the_current_branch_is_not_master_and_no_matching_file_paths_have_been_found(): void
    {
        $this->configRepository->shouldReceive('isEmpty')->once()->andReturn(false);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer", [])->andReturn([
            'paths' => [
                'assets/',
                'fruits/*/assets',
            ],
            'extensions' => ['js', 'jsx'],
        ]);
        $this->configRepository->shouldReceive("get")->with("tools.php_cs_fixer.paths")->andReturn([
            'assets/',
            'fruits/*/assets',
        ]);

        $this->gitFilesFinder->shouldReceive('getBranchName')->andReturn("pineapples");
        $this->git->shouldReceive('isEmpty')->once()->andReturn(false);

        $this->gitFilesFinder->shouldReceive('find')->once()->andReturn(collect([]));

        $this->process->shouldNotReceive('simple');
        $this->processor->shouldNotReceive('process');

        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals(\file_get_contents(__DIR__ . '/../../../fixtures/PhpCsFixer/non-master-no-files.txt'), $output);
    }

    private function getCommandTester(): CommandTester
    {
        $consoleApplication = new Application();

        $this->command->setLaravel(new Container());

        $consoleApplication->add($this->command);
        $command = $consoleApplication->find('php-cs-fixer');

        return new CommandTester($command);
    }
}
