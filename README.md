# Simple Container

This is a simple implementation of the [standard container interface](https://www.php-fig.org/psr/psr-11/), with support for autowiring.

This means that this package will attempt to figure out all your basic dependencies for you.
It also supports manually defining dependencies in a YAML file, or even adding service providers for more complex dependencies.

## Installation

This package requires at least PHP 7.2.

Install it using composer:
```
composer require gdetassigny/simple-container
```

## How to use it

### Basic usage

Setting up a basic container is quite simple:

```php
use GabrielDeTassigny\SimpleContainer\ContainerProvider;

$container = (new ContainerProvider())->getContainer();
```

That's it! You can now request your dependencies from the container, thanks to autowiring.

```php
$container->get(Foo\Bar::class); // returns an instance of Foo\Bar
``` 

### YAML Config

Simple container also supports adding a YAML config to manually define dependencies.

This can be useful for example to define a concrete class implementation for an interface.
Or you may want to define a different name for your service than its class name. 
```yaml
dependencies:
  Foo\Bar:
    name: Foo\BarInterface
  
  Foo\Baz:
    name: some-id
    dependencies:
      - Foo\BarInterface
```

Simply pass the path of the YAML file to the container provider:
```php
use GabrielDeTassigny\SimpleContainer\ContainerProvider;

$container = (new ContainerProvider('/path/to/config.yaml'))->getContainer();

$container->get(Foo\BarInterface::class); // returns an instance of Foo\Bar
$container->get('some-id'); // returns an instance of Foo\Baz
``` 

### Service Provider

If a service requires a more complex setup, you may want to look at using a service provider.

```php
use GabrielDeTassigny\SimpleContainer\ServiceProvider;
use GabrielDeTassigny\SimpleContainer\ContainerProvider;

class FooServiceProvider implements ServiceProvider
{
    public function getService(): object
    {
        return new Foo('some-string');
    }
}

$containerProvider = new ContainerProvider();
$containerProvider->registerService(Foo::class, new FooServiceProvider());

$container = $containerProvider->getContainer();

$container->get(Foo::class); // returns the instance of Foo defined in FooServiceProvider
```

### Ordering

When called, the container will look for your service using all 3 above options in that order:
- service provider
- YAML config
- autowiring

## Why use this instead of another container package?

To be honest? Maybe you shouldn't.
I built this mostly out of interest to understand how containers work under the hood.
There are a lot of more evolved PHP containers out there. 

However, I tried to make this package as straightforward to use as possible.
If having a container up and running very quickly appeals to you then feel free to give this a go!

Furthermore, as it respects the standard container interface, 
you can always try it for a bit and later switch to another container package without too much hassle.
