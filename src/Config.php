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
     * Supports dot notation for nested keys (e.g., 'app.debug').
     */
    public function has(string $key): bool
    {
        if (str_contains($key, '.')) {
            return $this->hasNested($key);
        }

        return array_key_exists($key, $this->values);
    }

    /**
     * {@inheritdoc}
     *
     * Gets a configuration value by key, or returns the fallback if the key doesn't exist.
     * Supports dot notation for nested keys (e.g., 'app.debug').
     */
    public function get(string $key, mixed $fallback = null): mixed
    {
        if (str_contains($key, '.')) {
            return $this->getNested($key, $fallback);
        }

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
     * Supports dot notation for nested keys (e.g., 'app.debug').
     */
    public function set(string $key, mixed $value): void
    {
        if (str_contains($key, '.')) {
            $this->setNested($key, $value);

            return;
        }

        $this->values[$key] = is_array($value) ? new self($value) : $value;
    }

    /**
     * {@inheritdoc}
     *
     * Removes a configuration key and its value.
     * Supports dot notation for nested keys (e.g., 'app.debug').
     */
    public function remove(string $key): void
    {
        if (str_contains($key, '.')) {
            $this->removeNested($key);

            return;
        }

        unset($this->values[$key]);
    }

    /**
     * Check if a nested configuration key exists using dot notation.
     *
     * @param  string  $key  The dot-notation key (e.g., 'app.debug')
     * @return bool True if the key exists, false otherwise
     */
    protected function hasNested(string $key): bool
    {
        $keys = explode('.', $key, 2);
        $firstKey = $keys[0];

        if (! array_key_exists($firstKey, $this->values)) {
            return false;
        }

        $value = $this->values[$firstKey];

        if (! ($value instanceof ConfigInterface)) {
            return count($keys) === 1;
        }

        if (count($keys) === 1) {
            return true;
        }

        return $value->has($keys[1]);
    }

    /**
     * Get a nested configuration value using dot notation.
     *
     * @param  string  $key  The dot-notation key (e.g., 'app.debug')
     * @param  mixed  $fallback  The fallback value if key doesn't exist
     * @return mixed The configuration value or fallback
     */
    protected function getNested(string $key, mixed $fallback = null): mixed
    {
        $keys = explode('.', $key, 2);
        $firstKey = $keys[0];

        if (! array_key_exists($firstKey, $this->values)) {
            return $fallback;
        }

        $value = $this->values[$firstKey];

        if (! ($value instanceof ConfigInterface)) {
            return count($keys) === 1 ? $value : $fallback;
        }

        if (count($keys) === 1) {
            return $value;
        }

        return $value->get($keys[1], $fallback);
    }

    /**
     * Set a nested configuration value using dot notation.
     *
     * @param  string  $key  The dot-notation key (e.g., 'app.debug')
     * @param  mixed  $value  The value to set
     */
    protected function setNested(string $key, mixed $value): void
    {
        $keys = explode('.', $key, 2);
        $firstKey = $keys[0];

        if (count($keys) === 1) {
            $this->values[$firstKey] = is_array($value) ? new self($value) : $value;

            return;
        }

        if (! array_key_exists($firstKey, $this->values)) {
            $this->values[$firstKey] = new self;
        }

        $nested = $this->values[$firstKey];

        if (! ($nested instanceof ConfigInterface)) {
            $this->values[$firstKey] = new self;
            $nested = $this->values[$firstKey];
        }

        $nested->set($keys[1], $value);
    }

    /**
     * Remove a nested configuration key using dot notation.
     *
     * @param  string  $key  The dot-notation key (e.g., 'app.debug')
     */
    protected function removeNested(string $key): void
    {
        $keys = explode('.', $key, 2);
        $firstKey = $keys[0];

        if (! array_key_exists($firstKey, $this->values)) {
            return;
        }

        if (count($keys) === 1) {
            unset($this->values[$firstKey]);

            return;
        }

        $nested = $this->values[$firstKey];

        if (! ($nested instanceof ConfigInterface)) {
            return;
        }

        $nested->remove($keys[1]);
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
