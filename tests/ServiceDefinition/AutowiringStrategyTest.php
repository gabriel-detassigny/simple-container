<?php

namespace GabrielDeTassigny\SimpleContainer\Tests\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\ContainerException;
use GabrielDeTassigny\SimpleContainer\Exception\NotFoundException;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\AutowiringStrategy;
use GabrielDeTassigny\SimpleContainer\Tests\Reflection;
use PHPUnit\Framework\TestCase;

class AutowiringStrategyTest extends TestCase
{
    /** @var AutowiringStrategy */
    private $strategy;

    public function setUp(): void
    {
        $this->strategy = new AutowiringStrategy();
    }

    public function testHasDefinitionReturnsFalseIfClassNotFound(): void
    {
        $this->assertFalse($this->strategy->hasDefinition('InvalidClass'));
    }

    /**
     * @dataProvider classNameProvider
     * @param string $className
     */
    public function testHasDefinitionReturnsTrueWithValidClass(string $className): void
    {
        $this->assertTrue($this->strategy->hasDefinition($className));
    }

    /**
     * @dataProvider classNameProvider
     * @param string $className
     * @param int $dependenciesCount
     */
    public function testGetDefinitionReturnsServiceDefinition(string $className, int $dependenciesCount): void
    {
        $serviceDefinition = $this->strategy->getDefinition($className);

        $this->assertCount($dependenciesCount, $serviceDefinition->getDependencies());
        $this->assertSame($className, $serviceDefinition->getName());
    }

    public function testGetDefinitionThrowsErrorIfClassNotFound(): void
    {
        $this->expectException(NotFoundException::class);

        $this->strategy->getDefinition('InvalidClass');
    }

    public function testGetDefinitionThrowsErrorForPrimitiveParam(): void
    {
        $className = Reflection\ConstructorWithPrimitiveParam::class;

        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Failed to autowire $className: parameter test is not a class!");

        $this->strategy->getDefinition($className);
    }

    public function classNameProvider(): array
    {
        return [
            'no constructor' => [Reflection\NoConstructor::class, 0],
            'empty constructor' => [Reflection\ConstructorNoParam::class, 0],
            'class as a dependency' => [Reflection\ConstructorWithClassParam::class, 1],
            'parameter with default value' => [Reflection\ConstructorWithDefaultParam::class, 0]
        ];
    }
}
