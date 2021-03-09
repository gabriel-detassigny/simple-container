<?php

namespace GabrielDeTassigny\SimpleContainer\Tests\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\NotFoundException;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\ServiceDefinition;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\ServiceDefinitionManager;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\ServiceDefinitionStrategy;
use Phake;
use PHPUnit\Framework\TestCase;

class ServiceDefinitionManagerTest extends TestCase
{
    private const SERVICE_ID = 'some-id';

    /** @var ServiceDefinitionManager */
    private $manager;

    /** @var ServiceDefinitionStrategy */
    private $strategy1;

    /** @var ServiceDefinitionStrategy */
    private $strategy2;

    /** @var ServiceDefinition */
    private $definition;

    protected function setUp(): void
    {
        $this->definition = new ServiceDefinition('service-name', []);

        $this->strategy1 = Phake::mock(ServiceDefinitionStrategy::class);
        $this->strategy2 = Phake::mock(ServiceDefinitionStrategy::class);

        $this->manager = new ServiceDefinitionManager($this->strategy1, $this->strategy2);
    }

    public function testGetDefinitionReturnsDefinitionFromStrategy(): void
    {
        Phake::when($this->strategy1)->hasDefinition(self::SERVICE_ID)->thenReturn(false);
        Phake::when($this->strategy2)->hasDefinition(self::SERVICE_ID)->thenReturn(true);

        Phake::when($this->strategy2)->getDefinition(self::SERVICE_ID)->thenReturn($this->definition);

        $this->assertSame($this->definition, $this->manager->getDefinition(self::SERVICE_ID));
    }

    public function testGetDefinitionThrowsErrorWhenNoSupportingStrategy(): void
    {
        $this->expectException(NotFoundException::class);

        Phake::when($this->strategy1)->hasDefinition(self::SERVICE_ID)->thenReturn(false);
        Phake::when($this->strategy2)->hasDefinition(self::SERVICE_ID)->thenReturn(false);

        $this->manager->getDefinition(self::SERVICE_ID);
    }

    public function testHasDefinitionReturnsTrueIfSupportingStrategy(): void
    {
        Phake::when($this->strategy1)->hasDefinition(self::SERVICE_ID)->thenReturn(true);
        Phake::when($this->strategy2)->hasDefinition(self::SERVICE_ID)->thenReturn(false);

        $this->assertTrue($this->manager->hasDefinition(self::SERVICE_ID));
    }

    public function testHasDefinitionReturnsFalseIfNoSupportingStrategy(): void
    {
        Phake::when($this->strategy1)->hasDefinition(self::SERVICE_ID)->thenReturn(false);
        Phake::when($this->strategy2)->hasDefinition(self::SERVICE_ID)->thenReturn(false);

        $this->assertFalse($this->manager->hasDefinition(self::SERVICE_ID));
    }
}
