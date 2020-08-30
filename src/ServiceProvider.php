<?php

declare(strict_types=1);

namespace GabrielDeTassigny\SimpleContainer;

use GabrielDeTassigny\SimpleContainer\Exception\ServiceCreationException;

interface ServiceProvider
{
    /**
     * @return object
     * @throws ServiceCreationException
     */
    public function getService(): object;
}