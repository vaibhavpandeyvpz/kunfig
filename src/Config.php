<?php

declare(strict_types=1);

/*
 * This file is part of vaibhavpandeyvpz/kunfig package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kunfig;

/**
 * Class Config
 *
 * Main configuration class that provides a flexible way to manage
 * configuration values with support for nested configurations,
 * array access, property access, and merging configurations.
 *
 * @author  Vaibhav Pandey <contact@vaibhavpandey.com>
 */
class Config implements ConfigInterface
{
    use ConfigTrait;

    /**
     * Internal storage for configuration values.
     *
     * @var array<string, mixed>
     */
    protected array $values = [];

    /**
     * Config constructor.
     *
     * Initializes the configuration with the provided values.
     * Array values are automatically converted to Config instances
     * for nested configuration support. All keys are cast to strings.
     *
     * @param  array<string, mixed>  $values  Initial configuration values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $key => $value) {
            $this->set((string) $key, $value);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Returns all configuration values as a plain array.
     * Nested ConfigInterface instances are recursively converted to arrays.
     */
    public function all(): array
    {
        $values = [];
        foreach ($this->values as $key => $value) {
            if ($value instanceof ConfigInterface) {
                $values[$key] = $value->all();
            } else {
                $values[$key] = $value;
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     *
     * Returns the number of configuration items (Countable implementation).
     */
    public function count(): int
    {
        return count($this->values);
    }

    /**
     * {@inheritdoc}
     *
     * Checks if a configuration key exists.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * {@inheritdoc}
     *
     * Gets a configuration value by key, or returns the fallback if the key doesn't exist.
     */
    public function get(string $key, mixed $fallback = null): mixed
    {
        return $this->has($key) ? $this->values[$key] : $fallback;
    }

    /**
     * {@inheritdoc}
     *
     * Returns an iterator for iterating over configuration values
     * (IteratorAggregate implementation).
     *
     * @return \ArrayIterator<string, mixed> Iterator over configuration values
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * {@inheritdoc}
     *
     * Merges another configuration into this one.
     * Values from the provided configuration override existing values.
     * Nested ConfigInterface instances are recursively merged.
     */
    public function mix(ConfigInterface $config): void
    {
        foreach ($config as $key => $value) {
            $key = (string) $key;
            if ($this->has($key)) {
                $preset = $this->get($key);
                if (($preset instanceof ConfigInterface) && ($value instanceof ConfigInterface)) {
                    $preset->mix($value);

                    continue;
                }
            }
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Sets a configuration value. If the value is an array,
     * it is automatically converted to a Config instance.
     */
    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = is_array($value) ? new self($value) : $value;
    }

    /**
     * {@inheritdoc}
     *
     * Removes a configuration key and its value.
     */
    public function remove(string $key): void
    {
        unset($this->values[$key]);
    }

    // <editor-fold desc="Magic Methods">

    /**
     * Creates a new instance from an exported array (var_export compatibility).
     *
     * This method is called when using var_export() on a Config instance.
     *
     * @param  array<string, mixed>  $data  The exported data array
     * @return static A new Config instance
     */
    public static function __set_state(array $data = []): static
    {
        return new self($data);
    }

    // </editor-fold>
}
