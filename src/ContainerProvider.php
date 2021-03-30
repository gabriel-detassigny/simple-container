<?php

declare(strict_types=1);

namespace GabrielDeTassigny\SimpleContainer;

use GabrielDeTassigny\SimpleContainer\Exception\InvalidContainerConfigException;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\AutowiringStrategy;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\ServiceDefinitionManager;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\ServiceProviderStrategy;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\YamlConfigStrategy;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Parser;

class ContainerProvider
{
    /** @var string|null */
    private $configPath;

    /** @var ServiceProviderStrategy */
    private $serviceProviderStrategy;

    public function __construct(?string $configPath = null)
    {
        $this->configPath = $configPath;
        $this->serviceProviderStrategy = new ServiceProviderStrategy();
    }

    /**
     * @return ContainerInterface
     * @throws InvalidContainerConfigException
     */
    public function getContainer(): ContainerInterface
    {
        $strategies = [$this->serviceProviderStrategy];
        if ($this->configPath) {
            $strategies[] = new YamlConfigStrategy(new Parser(), $this->configPath);
        }
        $strategies[] = new AutowiringStrategy();

        return new Container(new ServiceDefinitionManager(...$strategies));
    }

    public function registerService(string $id, ServiceProvider $serviceProvider): void
    {
        $this->serviceProviderStrategy->registerService($id, $serviceProvider);
    }
}
