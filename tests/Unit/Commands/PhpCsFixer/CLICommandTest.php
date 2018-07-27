<?php

namespace Shopworks\Tests\Unit\Commands\PhpCsFixer;

use Shopworks\Git\Review\Commands\PhpCsFixer\CLICommand;
use Shopworks\Tests\UnitTestCase;

class CLICommandTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_build_a_command_for_all_config_options(): void
    {
        $command = new CLICommand([
            'bin_path' => 'vendor/bin/php-cs-fixer',
            'config_path' => '.php_cs',
            'verbosity_level' => 1,
        ], ['app/', 'tests/*/examples']);

        $this->assertEquals(
            "php vendor/bin/php-cs-fixer fix app/ tests/*/examples --config=.php_cs --dry-run -v",
            $command->toString()
        );
    }

    /** @test */
    public function it_adds_the_necessary_defaults_when_no_config_is_provided_but_paths_are(): void
    {
        $command = new CLICommand([], ['app/', 'tests/*/examples']);

        $this->assertEquals("php vendor/bin/php-cs-fixer fix app/ tests/*/examples --dry-run", $command->toString());
    }

    /** @test */
    public function it_allows_user_to_disable_dry_run_mode_via_config(): void
    {
        $command = new CLICommand(['dry_run_mode' => false], ['app/', 'tests/*/examples']);

        $this->assertEquals("php vendor/bin/php-cs-fixer fix app/ tests/*/examples", $command->toString());
    }

    /** @test */
    public function it_can_handle_all_verbosity_levels(): void
    {
        $command = new CLICommand([
            'verbosity_level' => 1,
        ], ['app/', 'tests/*/examples']);

        $this->assertEquals("php vendor/bin/php-cs-fixer fix app/ tests/*/examples --dry-run -v", $command->toString());

        $command = new CLICommand([
            'verbosity_level' => 2,
        ], ['app/', 'tests/*/examples']);

        $this->assertEquals("php vendor/bin/php-cs-fixer fix app/ tests/*/examples --dry-run -vv", $command->toString());

        $command = new CLICommand([
            'verbosity_level' => 3,
        ], ['app/', 'tests/*/examples']);

        $this->assertEquals("php vendor/bin/php-cs-fixer fix app/ tests/*/examples --dry-run -vvv", $command->toString());
    }
}
