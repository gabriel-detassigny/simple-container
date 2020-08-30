<?php

declare(strict_types=1);

namespace GabrielDeTassigny\SimpleContainer\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\ContainerException;
use GabrielDeTassigny\SimpleContainer\Exception\ServiceCreationException;
use GabrielDeTassigny\SimpleContainer\ServiceProvider;

class ServiceProviderStrategy implements ServiceDefinitionStrategy
{
    /** @var ServiceProvider[] */
    private $serviceProviders = [];

    /**
     * {@inheritDoc}
     */
    public function getDefinition(string $id): ServiceDefinition
    {
        try {
            $instance = $this->serviceProviders[$id]->getService();
        } catch (ServiceCreationException $e) {
            throw new ContainerException("Error retrieving service {$id} from its provider");
        }

        return new ServiceDefinition(get_class($instance), [], $instance);
    }

    /**
     * {@inheritDoc}
     */
    public function hasDefinition(string $id): bool
    {
        return array_key_exists($id, $this->serviceProviders);
    }

    public function registerService(string $id, ServiceProvider $serviceProvider): void
    {
        $this->serviceProviders[$id] = $serviceProvider;
    }
}
