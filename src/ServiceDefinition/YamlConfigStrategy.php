<?php

declare(strict_types=1);

namespace GabrielDeTassigny\SimpleContainer\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\InvalidContainerConfigException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlConfigStrategy implements ServiceDefinitionStrategy
{
    /** @var ServiceDefinition[] */
    private $serviceDefinitions = [];

    public function __construct(Parser $yamlParser, string $configPath)
    {
        $this->loadDefinitionsFromConfig($yamlParser, $configPath);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefinition(string $id): ServiceDefinition
    {
        return $this->serviceDefinitions[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function hasDefinition(string $id): bool
    {
        return array_key_exists($id, $this->serviceDefinitions);
    }

    private function loadDefinitionsFromConfig(Parser $yamlParser, string $configPath): void
    {
        if (!file_exists($configPath)) {
            throw new InvalidContainerConfigException($configPath . ': YAML config file not found!');
        }

        try {
            $config = $yamlParser->parse(file_get_contents($configPath));
        } catch (ParseException $e) {
            throw new InvalidContainerConfigException('Error parsing YAML: ' . $e->getMessage());
        }

        foreach ($config['dependencies'] ?? [] as $id => $service) {
            $this->serviceDefinitions[$id] = new ServiceDefinition($service['name'], $service['dependencies'] ?? []);
        }
    }
}