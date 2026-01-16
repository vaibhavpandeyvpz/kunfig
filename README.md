# Kunfig

[![Tests](https://github.com/vaibhavpandeyvpz/kunfig/actions/workflows/tests.yml/badge.svg)](https://github.com/vaibhavpandeyvpz/kunfig/actions)
[![Latest Version](https://img.shields.io/packagist/v/vaibhavpandeyvpz/kunfig.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/kunfig)
[![Downloads](https://img.shields.io/packagist/dt/vaibhavpandeyvpz/kunfig.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/kunfig)
[![PHP Version](https://img.shields.io/packagist/php-v/vaibhavpandeyvpz/kunfig.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/kunfig)
[![License](https://img.shields.io/packagist/l/vaibhavpandeyvpz/kunfig.svg?style=flat-square)](LICENSE)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg?style=flat-square)](https://github.com/vaibhavpandeyvpz/kunfig)

A modern, flexible PHP configuration management library that provides multiple access patterns (methods, arrays, properties, and dot notation) for managing configuration values with support for nested configurations, merging, and type safety.

## Features

- 🎯 **Multiple Access Patterns**: Access configuration values using methods, array syntax, or object properties
- 🔄 **Nested Configurations**: Automatic conversion of arrays to nested `Config` instances
- 🔀 **Configuration Merging**: Recursively merge multiple configurations with `mix()`
- 📝 **Dot Notation**: Access nested values using dot syntax (e.g., `$config->get('app.debug')`)
- 🔒 **Type Safety**: Full PHP 8.2+ type declarations with strict types
- 📊 **Iterable & Countable**: Implements `IteratorAggregate` and `Countable` interfaces
- 🎨 **Modern PHP**: Built with PHP 8.2+ features including traits, union types, and more
- ✅ **100% Test Coverage**: Comprehensive test suite with full code coverage

## Requirements

- PHP 8.2 or higher

## Installation

Install via Composer:

```bash
composer require vaibhavpandeyvpz/kunfig
```

## Quick Start

```php
<?php

use Kunfig\Config;

// Create a configuration instance
$config = new Config([
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'myapp',
    ],
    'app' => [
        'name' => 'My Application',
        'debug' => false,
    ],
]);

// Access values using object properties
echo $config->database->host; // 'localhost'

// Access values using array syntax
echo $config['app']['name']; // 'My Application'

// Access values using dot notation
echo $config->get('app.name'); // 'My Application'
echo $config['database.host']; // 'localhost'

// Access values using methods
echo $config->get('database')->get('port'); // 3306
```

## Usage Examples

### Basic Operations

#### Creating a Configuration

```php
use Kunfig\Config;

// Empty configuration
$config = new Config();

// With initial values
$config = new Config([
    'key' => 'value',
    'nested' => [
        'deep' => 'value',
    ],
]);
```

#### Getting Values

```php
// Method access
$value = $config->get('key');
$value = $config->get('nonexistent', 'default'); // with fallback

// Property access
$value = $config->key;
$nested = $config->nested->deep;

// Array access
$value = $config['key'];
$nested = $config['nested']['deep'];

// Dot notation (alternative to nested access)
$nested = $config->get('nested.deep');
$nested = $config['nested.deep'];
$value = $config->get('app.database.host', 'localhost'); // with fallback
```

#### Setting Values

```php
// Method access
$config->set('key', 'value');
$config->set('nested', ['deep' => 'value']);

// Property access
$config->key = 'value';
$config->nested = new Config(['deep' => 'value']);

// Array access
$config['key'] = 'value';
$config['nested'] = ['deep' => 'value'];

// Dot notation (creates nested structure automatically)
$config->set('app.debug', true);
$config['database.host'] = 'localhost';
$config->set('app.database.port', 3306); // creates nested structure

// Arrays are automatically converted to Config instances
$config->set('database', ['host' => 'localhost']);
$config->database->host; // 'localhost' (automatically a Config instance)
```

#### Checking Existence

```php
// Method access
if ($config->has('key')) {
    // key exists
}

// Property access
if (isset($config->key)) {
    // key exists
}

// Array access
if (isset($config['key'])) {
    // key exists
}

// Dot notation
if ($config->has('app.debug')) {
    // nested key exists
}
if (isset($config['app.debug'])) {
    // nested key exists
}
```

#### Removing Values

```php
// Method access
$config->remove('key');

// Property access
unset($config->key);

// Array access
unset($config['key']);

// Dot notation
$config->remove('app.debug');
unset($config['database.host']);
```

### Dot Notation

Kunfig supports dot notation for accessing nested configuration values, providing a convenient alternative to chained property or array access.

#### Basic Usage

```php
$config = new Config([
    'app' => [
        'name' => 'MyApp',
        'debug' => false,
        'database' => [
            'host' => 'localhost',
            'port' => 3306,
        ],
    ],
]);

// Instead of: $config->app->database->host
$host = $config->get('app.database.host'); // 'localhost'

// Instead of: $config['app']['database']['port']
$port = $config['app.database.port']; // 3306

// With fallback
$timeout = $config->get('app.database.timeout', 30); // 30 (default)
```

#### Setting Nested Values

Dot notation automatically creates the nested structure when setting values:

```php
$config = new Config();

// Creates nested structure automatically
$config->set('app.debug', true);
$config['app.database.host'] = 'localhost';
$config->set('app.database.port', 3306);

// Now accessible via all methods
$config->app->debug; // true
$config['app']['database']['host']; // 'localhost'
$config->get('app.database.port'); // 3306
```

#### Checking Existence

```php
// Check nested keys
if ($config->has('app.debug')) {
    // app.debug exists
}

if (isset($config['app.database.host'])) {
    // app.database.host exists
}
```

#### Removing Nested Values

```php
// Remove nested keys
$config->remove('app.debug');
unset($config['app.database.port']);

// Remove entire nested branches
$config->remove('app.database'); // removes entire database config
```

#### Deeply Nested Access

Dot notation works with any depth of nesting:

```php
$config = new Config([
    'level1' => [
        'level2' => [
            'level3' => [
                'level4' => 'deep_value',
            ],
        ],
    ],
]);

// Access deeply nested values
$value = $config->get('level1.level2.level3.level4'); // 'deep_value'
$config->set('level1.level2.level3.level4.new', 'value');
```

#### Edge Cases

```php
$config = new Config([
    'app' => 'simple_string', // non-config value
]);

// Accessing nested key on non-config value returns fallback
$value = $config->get('app.key', 'default'); // 'default'

// Setting nested key on non-config value converts it to Config
$config->set('app.key', 'value');
$config->get('app'); // Returns ConfigInterface instance
$config->get('app.key'); // 'value'
```

### Merging Configurations

The `mix()` method allows you to merge configurations, with values from the source configuration overriding existing values. Nested configurations are recursively merged.

```php
$base = new Config([
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'production',
    ],
    'app' => [
        'name' => 'My App',
        'debug' => false,
    ],
]);

$override = new Config([
    'database' => [
        'port' => 5432, // Override port
        'name' => 'staging', // Override name
        // host remains 'localhost'
    ],
    'app' => [
        'debug' => true, // Override debug
        // name remains 'My App'
    ],
    'cache' => [
        'enabled' => true, // New key
    ],
]);

$base->mix($override);

// Result:
// $base->database->host === 'localhost' (unchanged)
// $base->database->port === 5432 (overridden)
// $base->database->name === 'staging' (overridden)
// $base->app->name === 'My App' (unchanged)
// $base->app->debug === true (overridden)
// $base->cache->enabled === true (new)
```

### Iterating Over Configuration

Since `Config` implements `IteratorAggregate`, you can iterate over configuration values:

```php
$config = new Config([
    'key1' => 'value1',
    'key2' => 'value2',
    'key3' => 'value3',
]);

foreach ($config as $key => $value) {
    echo "$key: $value\n";
}
```

### Getting All Values

The `all()` method returns all configuration values as a plain array, recursively converting nested `Config` instances:

```php
$config = new Config([
    'app' => [
        'name' => 'My App',
        'version' => '1.0.0',
    ],
    'debug' => false,
]);

$array = $config->all();
// Returns:
// [
//     'app' => [
//         'name' => 'My App',
//         'version' => '1.0.0',
//     ],
//     'debug' => false,
// ]
```

### Counting Configuration Items

Since `Config` implements `Countable`, you can use `count()`:

```php
$config = new Config([
    'key1' => 'value1',
    'key2' => 'value2',
    'key3' => 'value3',
]);

echo count($config); // 3
```

### Real-World Example

```php
<?php

use Kunfig\Config;

// Load base configuration
$config = new Config([
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'charset' => 'utf8mb4',
    ],
    'cache' => [
        'driver' => 'file',
        'ttl' => 3600,
    ],
]);

// Load environment-specific overrides
$envConfig = new Config([
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'myapp',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
    ],
    'cache' => [
        'ttl' => 7200, // Override TTL
    ],
]);

// Merge configurations
$config->mix($envConfig);

// Use the configuration (multiple access patterns)
$pdo = new PDO(
    sprintf(
        "mysql:host=%s;port=%d;dbname=%s;charset=%s",
        $config->database->host,              // Property access
        $config->get('database.port'),        // Dot notation
        $config['database']['name'],          // Array access
        $config->get('database')->get('charset') // Method chaining
    ),
    $config->get('database.user'),            // Dot notation
    $config->database->password               // Property access
);
```

## API Reference

### ConfigInterface

The main interface that defines the configuration contract.

#### Methods

- `all(): array` - Get all configuration values as an array
- `has(string $key): bool` - Check if a key exists (supports dot notation)
- `get(string $key, mixed $fallback = null): mixed` - Get a value by key (supports dot notation)
- `set(string $key, mixed $value): void` - Set a value (supports dot notation, creates nested structure)
- `remove(string $key): void` - Remove a key (supports dot notation)
- `mix(ConfigInterface $config): void` - Merge another configuration

#### Implemented Interfaces

- `ArrayAccess` - Array-like access (`$config['key']` or `$config['app.debug']` with dot notation)
- `Countable` - Count items (`count($config)`)
- `IteratorAggregate` - Iterate over values (`foreach ($config as ...)`)

### Config

The main configuration class implementing `ConfigInterface`.

#### Constructor

```php
public function __construct(array $values = [])
```

Creates a new `Config` instance. Array values are automatically converted to nested `Config` instances.

#### Static Methods

```php
public static function __set_state(array $data): static
```

Creates a new instance from an exported array (for `var_export()` compatibility).

### ConfigTrait

A trait that provides default implementations for `ArrayAccess` and property access methods. Can be used by any class implementing `ConfigInterface`.

## Advanced Usage

### Custom Configuration Classes

You can create custom configuration classes by implementing `ConfigInterface` and using `ConfigTrait`:

```php
use Kunfig\ConfigInterface;
use Kunfig\ConfigTrait;

class MyCustomConfig implements ConfigInterface
{
    use ConfigTrait;

    protected array $values = [];

    // Implement required methods...
    public function all(): array { /* ... */ }
    public function has(string $key): bool { /* ... */ }
    // ... etc
}
```

### Type-Safe Configuration

With PHP 8.2+ type declarations, you get full type safety:

```php
$config = new Config([
    'port' => 3306,
]);

// Type-safe access
$port = $config->get('port', 0); // int
$host = $config->get('host', 'localhost'); // string
```

### Handling Different Value Types

```php
$config = new Config([
    'string' => 'text',
    'integer' => 42,
    'float' => 3.14,
    'boolean' => true,
    'null' => null,
    'array' => ['nested' => 'value'],
]);

// All types are preserved
$config->string;   // string
$config->integer;  // int
$config->float;    // float
$config->boolean;  // bool
$config->null;     // null
$config->array;    // ConfigInterface instance
```

## Testing

The project maintains 100% code coverage. Run tests with:

```bash
# Run tests
vendor/bin/phpunit

# Run tests with coverage
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Links

- [GitHub Repository][github-url]
- [Packagist][packagist-url]
- [Issues][issues-url]

[github-url]: https://github.com/vaibhavpandeyvpz/kunfig
[packagist-url]: https://packagist.org/packages/vaibhavpandeyvpz/kunfig
[issues-url]: https://github.com/vaibhavpandeyvpz/kunfig/issues
