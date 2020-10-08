<?php

namespace GabrielDeTassigny\SimpleContainer\Tests;

use GabrielDeTassigny\SimpleContainer\ContainerProvider;
use GabrielDeTassigny\SimpleContainer\ServiceProvider;
use Phake;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ContainerProviderTest extends TestCase
{
    private const SERVICE_ID = 'some-service';

    /** @var ContainerProvider */
    private $containerProvider;

    protected function setUp(): void
    {
        $this->containerProvider = new ContainerProvider();
    }

    public function testCanCreateContainerWithoutParameter(): void
    {
        $this->assertInstanceOf(
            ContainerInterface::class,
            $this->containerProvider->getContainer()
        );
    }

    public function testCanCreateContainerWithYamlConfig(): void
    {
        $containerProvider = new ContainerProvider(__DIR__ . '/config/config.yaml');

        $this->assertInstanceOf(ContainerInterface::class, $containerProvider->getContainer());
    }

    public function testCanRegisterServiceProvider(): void
    {
        $serviceProvider = Phake::mock(ServiceProvider::class);

        $this->containerProvider->registerService(self::SERVICE_ID, $serviceProvider);

        $container = $this->containerProvider->getContainer();
        $this->assertTrue($container->has(self::SERVICE_ID));
    }
}
