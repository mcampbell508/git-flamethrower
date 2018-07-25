<?php

namespace Shopworks\Git\Review\Repositories;

use Illuminate\Support\Arr;
use Shopworks\Git\Review\Yml\YmlConfiguration;
use Symfony\Component\Yaml\Yaml;

class ConfigRepository
{
    private $config = [];

    public function __construct(YmlConfiguration $ymlConfiguration, Yaml $yamlParser)
    {
        $configPath = $ymlConfiguration->getConfigPath() ? \file_get_contents($ymlConfiguration->getConfigPath()) : '';

        $this->config = $yamlParser->parse($configPath);
    }

    public function get(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    public function isEmpty(): bool
    {
        return empty($this->config);
    }
}
