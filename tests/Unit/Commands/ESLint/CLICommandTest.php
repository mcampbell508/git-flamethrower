<?php

namespace Shopworks\Tests\Unit\Commands\ESLint;

use Shopworks\Git\Review\Commands\ESLint\CLICommand;
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
            'bin_path' => 'node_modules/.bin/eslint',
            'config_path' => '.eslintrc',
            'extensions' => [
                'js',
                'jsx',
            ],
        ], ['app/', 'tests/*/examples']);

        $this->assertEquals(
            "node_modules/.bin/eslint --ext js --ext jsx app/ tests/*/examples -c .eslintrc",
            $command->toString()
        );
    }

    /** @test */
    public function it_adds_the_necessary_defaults_when_no_config_is_provided_but_paths_are(): void
    {
        $command = new CLICommand([], ['app/', 'tests/*/examples']);

        $this->assertEquals("node_modules/.bin/eslint app/ tests/*/examples", $command->toString());
    }
}
