<?php

declare(strict_types=1);

namespace GabrielDeTassigny\SimpleContainer\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\NotFoundException;
use ReflectionClass;

class AutowiringStrategy implements ServiceDefinitionStrategy
{
    /**
     * {@inheritDoc}
     */
    public function getDefinition(string $id): ServiceDefinition
    {
        if (!$this->hasDefinition($id)) {
            throw new NotFoundException("Autowiring failure: Could not find class $id");
        }

        return new ServiceDefinition($id, $this->findDependencies($id));
    }

    /**
     * {@inheritDoc}
     */
    public function hasDefinition(string $id): bool
    {
        return class_exists($id);
    }

    private function findDependencies(string $id): array
    {
        $reflection = new ReflectionClass($id);

        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return [];
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $dependencies[] = $parameter->getClass()->getName();
        }

        return $dependencies;
    }
}