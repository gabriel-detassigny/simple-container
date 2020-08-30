<?php

declare(strict_types=1);

namespace GabrielDeTassigny\SimpleContainer\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\ContainerException;
use GabrielDeTassigny\SimpleContainer\Exception\NotFoundException;

interface ServiceDefinitionStrategy
{
    /**
     * @param string $id
     * @return ServiceDefinition
     * @throws NotFoundException
     * @throws ContainerException
     */
    public function getDefinition(string $id): ServiceDefinition;

    /**
     * @param string $id
     * @return bool
     */
    public function hasDefinition(string $id): bool;
}
