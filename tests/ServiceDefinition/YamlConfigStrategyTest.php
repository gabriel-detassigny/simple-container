<?php

namespace GabrielDeTassigny\SimpleContainer\Tests\ServiceDefinition;

use GabrielDeTassigny\SimpleContainer\Exception\InvalidContainerConfigException;
use GabrielDeTassigny\SimpleContainer\Exception\NotFoundException;
use GabrielDeTassigny\SimpleContainer\ServiceDefinition\YamlConfigStrategy;
use Phake;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlConfigStrategyTest extends TestCase
{
    private const SERVICE_ID = 'test-id';
    private const SERVICE_NAME = 'test';
    private const SERVICE_DEPENDENCIES = ['foo', 'bar'];

    private const CONFIG_PATH = __DIR__ . '/../config/config.yaml';
    private const CONFIG_DATA = [
        'dependencies' => [
            self::SERVICE_ID => [
                'name' => self::SERVICE_NAME,
                'dependencies' => self::SERVICE_DEPENDENCIES
            ]
        ]
    ];

    /** @var YamlConfigStrategy */
    private $strategy;

    /** @var Parser */
    private $yamlParser;

    protected function setUp(): void
    {
        $this->yamlParser = Phake::mock(Parser::class);
        Phake::when($this->yamlParser)->parse(Phake::anyParameters())
            ->thenReturn(self::CONFIG_DATA);

        $this->strategy = new YamlConfigStrategy($this->yamlParser, self::CONFIG_PATH);
    }

    public function testHasDefinitionThrowsErrorIfConfigNotFound(): void
    {
        $this->expectException(InvalidContainerConfigException::class);

        $this->strategy = new YamlConfigStrategy($this->yamlParser, 'invalid/config/path.yaml');

        $this->strategy->hasDefinition(self::SERVICE_ID);
    }

    public function testHasDefinitionThrowsErrorIfConfigIsNotValidYaml(): void
    {
        $this->expectException(InvalidContainerConfigException::class);

        Phake::when($this->yamlParser)->parse(Phake::anyParameters())
            ->thenThrow(new ParseException('parsing error'));

        $this->strategy->hasDefinition(self::SERVICE_ID);
    }

    public function testHasDefinitionReturnsTrueIfServiceFound(): void
    {
        $this->assertTrue($this->strategy->hasDefinition(self::SERVICE_ID));
    }

    public function testHasDefinitionReturnsFalseIfServiceNotFound(): void
    {
        $this->assertFalse($this->strategy->hasDefinition('not-found'));
    }

    public function testHasDefinitionLoadsConfigOnlyOnce(): void
    {
        $this->strategy->hasDefinition(self::SERVICE_ID);
        $this->strategy->hasDefinition('some-other-service');

        Phake::verify($this->yamlParser, Phake::times(1))->parse(Phake::anyParameters());
    }

    public function testGetDefinitionReturnsServiceDefinition(): void
    {
        $definition = $this->strategy->getDefinition(self::SERVICE_ID);

        $this->assertSame(self::SERVICE_NAME, $definition->getName());
        $this->assertSame(self::SERVICE_DEPENDENCIES, $definition->getDependencies());
    }

    public function testGetDefinitionThrowsErrorIfServiceNotFound(): void
    {
        $this->expectException(NotFoundException::class);

        $this->strategy->getDefinition('not-found');
    }
}
