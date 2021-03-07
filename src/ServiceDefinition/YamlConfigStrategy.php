<?php

declare(strict_types=1);

namespace GabrielDeTassigny\SimpleContainer\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\InvalidContainerConfigException;
use GabrielDeTassigny\SimpleContainer\Exception\NotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlConfigStrategy implements ServiceDefinitionStrategy
{
    /** @var ServiceDefinition[] */
    private $serviceDefinitions = [];

    /** @var Parser */
    private $yamlParser;

    /** @var string */
    private $configPath;

    /** @var bool */
    private $hasLoadedConfig = false;

    public function __construct(Parser $yamlParser, string $configPath)
    {
        $this->yamlParser = $yamlParser;
        $this->configPath = $configPath;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefinition(string $id): ServiceDefinition
    {
        if (!$this->hasDefinition($id)) {
            throw new NotFoundException("Service $id not found in YAML");
        }

        return $this->serviceDefinitions[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function hasDefinition(string $id): bool
    {
        $this->loadDefinitionsFromConfig();

        return array_key_exists($id, $this->serviceDefinitions);
    }

    private function loadDefinitionsFromConfig(): void
    {
        if ($this->hasLoadedConfig) {
            return;
        }

        if (!file_exists($this->configPath)) {
            throw new InvalidContainerConfigException($this->configPath . ': YAML config file not found!');
        }

        try {
            $config = $this->yamlParser->parse(file_get_contents($this->configPath));
        } catch (ParseException $e) {
            throw new InvalidContainerConfigException('Error parsing YAML: ' . $e->getMessage());
        }

        foreach ($config['dependencies'] ?? [] as $id => $service) {
            $this->serviceDefinitions[$id] = new ServiceDefinition($service['name'], $service['dependencies'] ?? []);
        }

        $this->hasLoadedConfig = true;
    }
}