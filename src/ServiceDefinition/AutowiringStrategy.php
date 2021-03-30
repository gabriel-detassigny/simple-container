<?php

declare(strict_types=1);

namespace GabrielDeTassigny\SimpleContainer\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\ContainerException;
use GabrielDeTassigny\SimpleContainer\Exception\NotFoundException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class AutowiringStrategy implements ServiceDefinitionStrategy
{
    /**
     * {@inheritDoc}
     */
    public function hasDefinition(string $id): bool
    {
        return class_exists($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefinition(string $id): ServiceDefinition
    {
        return new ServiceDefinition($id, $this->findDependencies($id));
    }

    /**
     * @param string $id
     * @return array
     * @throws ContainerException
     * @throws NotFoundException
     */
    private function findDependencies(string $id): array
    {
        try {
            $reflection = new ReflectionClass($id);
        } catch (ReflectionException $e) {
            throw new NotFoundException("Autowiring failure: Could not find class $id");
        }

        $constructor = $reflection->getConstructor();

        return $constructor ? $this->findConstructorParams($constructor, $id) : [];
    }

    /**
     * @param ReflectionMethod $constructor
     * @param string $id
     * @return array
     * @throws ContainerException
     */
    private function findConstructorParams(ReflectionMethod $constructor, string $id): array
    {
        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            if ($parameter->isOptional()) {
                continue;
            }

            $paramClass = $parameter->getClass();
            if (is_null($paramClass)) {
                throw new ContainerException("Failed to autowire $id: parameter {$parameter->name} is not a class!");
            }

            $dependencies[] = $paramClass->getName();
        }

        return $dependencies;
    }
}
