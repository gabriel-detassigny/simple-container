<?php

namespace GabrielDeTassigny\SimpleContainer\Tests\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\ContainerException;
use GabrielDeTassigny\SimpleContainer\Exception\ServiceCreationException;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\ServiceProviderStrategy;
use GabrielDeTassigny\SimpleContainer\ServiceProvider;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use stdClass;

class ServiceProviderStrategyTest extends TestCase
{
    private const SERVICE_ID = 'some-service';

    /** @var ServiceProviderStrategy */
    private $strategy;

    /** @var ServiceProvider|Phake_IMock */
    private $serviceProvider;

    protected function setUp(): void
    {
        $this->serviceProvider = Phake::mock(ServiceProvider::class);
        $this->strategy = new ServiceProviderStrategy();
    }

    public function testHasDefinitionReturnsFalseIfNotFound(): void
    {
        $this->assertFalse($this->strategy->hasDefinition(self::SERVICE_ID));
    }

    public function testHasDefinitionReturnsTrueIfServiceWasRegistered(): void
    {
        $this->strategy->registerService(self::SERVICE_ID, $this->serviceProvider);

        $this->assertTrue($this->strategy->hasDefinition(self::SERVICE_ID));
    }

    public function testGetDefinitionThrowsErrorIfServiceCreationFailed(): void
    {
        $this->strategy->registerService(self::SERVICE_ID, $this->serviceProvider);

        Phake::when($this->serviceProvider)->getService()
            ->thenThrow(new ServiceCreationException());

        $this->expectException(ContainerException::class);

        $this->strategy->getDefinition(self::SERVICE_ID);
    }

    public function testGetDefinitionReturnsServiceDefinitionFromProvider(): void
    {
        $expectedInstance = new stdClass();
        Phake::when($this->serviceProvider)->getService()
            ->thenReturn($expectedInstance);

        $this->strategy->registerService(self::SERVICE_ID, $this->serviceProvider);
        $serviceDefinition = $this->strategy->getDefinition(self::SERVICE_ID);

        $this->assertSame(stdClass::class, $serviceDefinition->getName());
        $this->assertEmpty($serviceDefinition->getDependencies());
        $this->assertSame($expectedInstance, $serviceDefinition->getInstance());
    }
}
