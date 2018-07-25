<?php

namespace Shopworks\Tests\Unit\Repositories;

use Mockery;
use Shopworks\Git\Review\Repositories\ConfigRepository;
use Shopworks\Git\Review\Yml\YmlConfiguration;
use Shopworks\Tests\UnitTestCase;
use Symfony\Component\Yaml\Yaml;

class ConfigRepositoryTest extends UnitTestCase
{
    /** @var Mockery\MockInterface|YmlConfiguration $ymlConfig */
    private $ymlConfig;
    /** @var Yaml $ymlParser */
    private $ymlParser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ymlConfig = Mockery::mock(YmlConfiguration::class);
        $this->ymlParser = new Yaml();
    }

    /** @test */
    public function it_returns_an_empty_configuration_when_no_yml_config_is_found(): void
    {
        $this->ymlConfig->shouldReceive('getConfigPath')->once()->andReturn(null);
        $repository = new ConfigRepository($this->ymlConfig, $this->ymlParser);

        $this->assertTrue($repository->isEmpty());
        $this->assertNull($repository->get('non_existent'));
    }

    /** @test */
    public function it_can_retrieve_config_using_a_simple_key(): void
    {
        $this->ymlConfig->shouldReceive('getConfigPath')->times(2)->andReturn('tests/fixtures/git-review.yml.dist');
        $repository = new ConfigRepository($this->ymlConfig, $this->ymlParser);

        $this->assertFalse($repository->isEmpty());

        $expected = ['configuration' => [
            'another_level',
        ]];

        $this->assertEquals($expected, $repository->get('test'));
    }

    /** @test */
    public function it_can_retrive_config_using_dot_notation(): void
    {
        $this->ymlConfig->shouldReceive('getConfigPath')->times(2)->andReturn('tests/fixtures/git-review.yml.dist');
        $repository = new ConfigRepository($this->ymlConfig, $this->ymlParser);

        $this->assertFalse($repository->isEmpty());

        $expected = [
            'another_level',
        ];

        $this->assertEquals($expected, $repository->get('test.configuration'));
    }

    /** @test */
    public function it_can_return_a_default_value_if_no_key_is_found(): void
    {
        $this->ymlConfig->shouldReceive('getConfigPath')->times(2)->andReturn('tests/fixtures/git-review.yml.dist');
        $repository = new ConfigRepository($this->ymlConfig, $this->ymlParser);

        $this->assertFalse($repository->isEmpty());
        $this->assertEquals('return-me', $repository->get('test.configuration.non-existent-item', 'return-me'));
    }
}
