<?php

declare(strict_types=1);

/*
 * This file is part of vaibhavpandeyvpz/kunfig package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Kunfig;

/**
 * Trait ConfigTrait
 *
 * Provides default implementation for ConfigInterface with support for
 * ArrayAccess and property access patterns.
 *
 * This trait can be used by any class that implements ConfigInterface
 * to get array and property access functionality.
 *
 * @author  Vaibhav Pandey <contact@vaibhavpandey.com>
 */
trait ConfigTrait
{
    // <editor-fold desc="Array Access">

    /**
     * Check if an offset exists (ArrayAccess implementation).
     *
     * Only string offsets are supported. Non-string offsets will return false.
     *
     * @param  mixed  $offset  The offset to check
     * @return bool True if the offset exists and is a string, false otherwise
     */
    public function offsetExists(mixed $offset): bool
    {
        return is_string($offset) && $this->has($offset);
    }

    /**
     * Get an offset value (ArrayAccess implementation).
     *
     * Only string offsets are supported. Non-string offsets will return null.
     *
     * @param  mixed  $offset  The offset to retrieve
     * @return mixed The value at the offset, or null if offset is not a string
     */
    public function offsetGet(mixed $offset): mixed
    {
        return is_string($offset) ? $this->get($offset) : null;
    }

    /**
     * Set an offset value (ArrayAccess implementation).
     *
     * Only string offsets are supported. Non-string offsets will be ignored.
     *
     * @param  mixed  $offset  The offset to set
     * @param  mixed  $value  The value to set
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_string($offset)) {
            $this->set($offset, $value);
        }
    }

    /**
     * Unset an offset (ArrayAccess implementation).
     *
     * Only string offsets are supported. Non-string offsets will be ignored.
     *
     * @param  mixed  $offset  The offset to unset
     */
    public function offsetUnset(mixed $offset): void
    {
        if (is_string($offset)) {
            $this->remove($offset);
        }
    }

    // </editor-fold>

    // <editor-fold desc="Property Access">

    /**
     * Magic method for property access.
     *
     * Allows accessing configuration values as object properties.
     * Example: $config->database->host
     *
     * @param  string  $name  The property name (configuration key)
     * @return mixed The configuration value
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /**
     * Magic method to check if a property is set.
     *
     * Allows using isset() on configuration values.
     * Example: isset($config->database)
     *
     * @param  string  $name  The property name (configuration key)
     * @return bool True if the property exists, false otherwise
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * Magic method for setting property values.
     *
     * Allows setting configuration values as object properties.
     * Example: $config->database->host = 'localhost'
     *
     * @param  string  $name  The property name (configuration key)
     * @param  mixed  $value  The value to set
     */
    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    /**
     * Magic method for unsetting properties.
     *
     * Allows using unset() on configuration values.
     * Example: unset($config->database)
     *
     * @param  string  $name  The property name (configuration key)
     */
    public function __unset(string $name): void
    {
        $this->remove($name);
    }

    // </editor-fold>
}
