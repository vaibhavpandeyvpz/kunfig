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
 * Interface ConfigInterface
 *
 * Configuration interface that provides methods for managing configuration values.
 * Extends ArrayAccess for array-like access, Countable for counting items,
 * and IteratorAggregate for iteration support.
 *
 * @author  Vaibhav Pandey <contact@vaibhavpandey.com>
 */
interface ConfigInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Get all configuration values as an array.
     *
     * Recursively converts nested ConfigInterface instances to arrays.
     *
     * @return array<string, mixed> All configuration values
     */
    public function all(): array;

    /**
     * Check if a configuration key exists.
     *
     * @param  string  $key  The configuration key to check
     * @return bool True if the key exists, false otherwise
     */
    public function has(string $key): bool;

    /**
     * Get a configuration value by key.
     *
     * Returns the value for the given key, or the fallback value if the key
     * does not exist. If the value is a nested array, it will be returned
     * as a ConfigInterface instance.
     *
     * @param  string  $key  The configuration key
     * @param  mixed  $fallback  The fallback value if key doesn't exist
     * @return ConfigInterface|mixed The configuration value or fallback
     */
    public function get(string $key, mixed $fallback = null): mixed;

    /**
     * Set a configuration value.
     *
     * If the value is an array, it will be automatically converted to a
     * ConfigInterface instance for nested configuration support.
     *
     * @param  string  $key  The configuration key
     * @param  mixed  $value  The value to set
     */
    public function set(string $key, mixed $value): void;

    /**
     * Remove a configuration key.
     *
     * @param  string  $key  The configuration key to remove
     */
    public function remove(string $key): void;

    /**
     * Merge another configuration into this one.
     *
     * Values from the provided configuration will override existing values.
     * Nested ConfigInterface instances will be recursively merged.
     *
     * @param  ConfigInterface  $config  The configuration to merge
     */
    public function mix(ConfigInterface $config): void;
}
