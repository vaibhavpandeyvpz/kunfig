<?php

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
 * Interface ConfigInterface
 * @package Kunfig
 */
interface ConfigInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @return array
     */
    public function all();

    /**
     * @param string $key
     * @return boolean
     */
    public function has($key);

    /**
     * @param string $key
     * @param mixed $fallback
     * @return ConfigInterface|mixed
     */
    public function get($key, $fallback = null);

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * @param string $key
     */
    public function remove($key);

    /**
     * @param ConfigInterface $config
     */
    public function mix(ConfigInterface $config);
}
