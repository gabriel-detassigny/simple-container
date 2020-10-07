<?php

namespace GabrielDeTassigny\SimpleContainer\Tests;

use GabrielDeTassigny\SimpleContainer\Container;
use GabrielDeTassigny\SimpleContainer\Exception\NotFoundException;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\ServiceDefinition;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\ServiceDefinitionStrategy;
use Phake;
use Phake_IMock;
use PHPUnit\Framework\TestCase;
use stdClass;

class ContainerTest extends TestCase
{
    private const ID = 'some-id';

    /** @var Container */
    private $container;

    /** @var ServiceDefinitionStrategy|Phake_IMock */
    private $strategy;

    protected function setUp(): void
    {
        $this->strategy = Phake::mock(ServiceDefinitionStrategy::class);
        Phake::when($this->strategy)->hasDefinition(self::ID)->thenReturn(true);

        $this->container = new Container($this->strategy);
    }

    public function testHasReturnsTrueIfStrategyHasDefinition(): void
    {
        $this->assertTrue($this->container->has(self::ID));
    }

    public function testHasReturnsFalseIfStrategyDoesNotHaveDefinition(): void
    {
        Phake::when($this->strategy)->hasDefinition(self::ID)->thenReturn(false);

        $this->assertFalse($this->container->has(self::ID));
    }

    public function testGetThrowsErrorIfStrategyDoesNotHaveDefinition(): void
    {
        Phake::when($this->strategy)->hasDefinition(self::ID)->thenReturn(false);

        $this->expectException(NotFoundException::class);

        $this->container->get(self::ID);
    }

    public function testGetReturnsInstanceFromStrategyDirectly(): void
    {
        $expected = new stdClass();
        Phake::when($this->strategy)->getDefinition(self::ID)
            ->thenReturn(new ServiceDefinition(stdClass::class, [], $expected));

        $actual = $this->container->get(self::ID);

        $this->assertSame($expected, $actual);
    }

    public function testGetReturnsInstanceWithoutDependencies(): void
    {
        Phake::when($this->strategy)->getDefinition(self::ID)
            ->thenReturn(new ServiceDefinition(stdClass::class, []));

        $actual = $this->container->get(self::ID);

        $this->assertInstanceOf(stdClass::class, $actual);
    }

    public function testGetInstantiateOnlyOnceForTwoCalls(): void
    {
        Phake::when($this->strategy)->getDefinition(self::ID)
            ->thenReturn(new ServiceDefinition(stdClass::class, []));

        $actual1 = $this->container->get(self::ID);
        $actual2 = $this->container->get(self::ID);

        $this->assertSame($actual1, $actual2);
        Phake::verify($this->strategy)->getDefinition(self::ID);
    }
}
